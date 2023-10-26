<?php

class SociosController{

    public function __construct(private SociosGateway $sociosGateway){}


    public function processRequest(string $method , ?string $id):void 
    {
        if($id){
            $this->processResurcesRequest($method , $id);
        }else{
            $this->processCollectionRequest($method);
        }
    }

    private function processResurcesRequest(string $method , string $id) : void {
        $socio = $this->sociosGateway->get($id);

        if(!$socio){
            http_response_code(404); // not found
            echo json_encode(['error' => 'Socio not founded']);
            return;
        }

        switch($method){
            case "GET":
                echo json_encode($socio);
                break;



                
            case "DELETE":
                $rows = $this->sociosGateway->delete($id);

                echo json_encode([
                    "message" =>"socio $id deleted",
                    "deleted rows"=>$rows
                ]);
                break;



            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"),true);
                $errors = $this->getValidationError($data,false);

                if(!empty($errors)){
                    http_response_code(422); // unprocessable entity
                    echo json_encode(["errors"=>$errors]);
                    break;
                }

                $rows = $this->sociosGateway->update($socio,$data);


                echo json_encode([
                    "message" => "socio $id updated",
                    "updatedRows" => $rows
                ]);





                break;
            default:
            http_response_code(405); // method not implemented
            header("Allow:GET,PATCH,DELETE");

        }





    }
    private function processCollectionRequest(string $method ) : void {
        switch($method){
            case 'GET':
                echo json_encode($this->sociosGateway->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents("php://input" , true));
               
                $errors = $this->getValidationError($data);

                if(!empty($errors)){
                    http_response_code(422); // unprocessable entity

                    echo json_encode(["errors" => $errors]);
                    break;
                }



                $id = $this->sociosGateway->create($data);

                http_response_code(201); // elemento creado

                echo json_encode([
                    "message" => "Socio creado",
                    "id" => $id
                ]);
                
                break;
            default:
                http_response_code(405); //method not allowed
                header("Allow:GET,POST"); //INFORMAR DISPONIBLES
        }
    }



    private function getValidationError(array $data , bool $is_new = true) : array{
        $errors = [];

        //-------------------- name validation -------------------------


        if($is_new && empty($data["nombre"])){
            $errors[] = 'name is required';
        }
        
        if(array_key_exists('nombre', $data)){
            $name = intval($data["nombre"]);

            if($name + 5 !==5){
                $errors[] = 'name most be a String';
            }
        }


        //----------------- telefon validation -------------------

        if(array_key_exists('telefono', $data)){
            if(intval($data['telefono']) + 0 === 0 || $data['telefono'] === 0 ){
                $errors[] = 'telefono must be an INTEGER';
            }
        }
        //--------------------------- age validation -----------------

        if(array_key_exists('edad', $data)){
            if(filter_var($data['edad'] , FILTER_VALIDATE_INT) === false){
                $errors[] = 'Age must be an INTEGER';
            }
        }


        //-------------- penalizado validation --------------------------

        if(array_key_exists('penalizado', $data)){
            if(filter_var($data['penalizado'] , FILTER_VALIDATE_BOOL) === false){
                $errors[] = 'penalizado must be BOOLEAN';
            }
        }




        return $errors;

    }



}