<?php

class PistasController{



    public function __construct(private PistasGateway $pistasGateway){}

    public function processRequest(string $method , ?string $id):void 
    {
        if($id){
            $this->processResurcesRequest($method , $id);
        }else{
            $this->processCollectionRequest($method);
        }
    }

    private function processResurcesRequest(string $method , string $id) : void {
        $pista = $this->pistasGateway->get($id);

        if(!$pista){
            http_response_code(404); // not found
            echo json_encode(['error' => 'pista not founded']);
            return;
        }

        switch($method){
            case "GET":
                echo json_encode($pista);
                break;


            case "DELETE":
                $rows = $this->pistasGateway->delete($id);

                echo json_encode([
                    "message" =>"socio $id deleted",
                    "deleted rows"=>$rows
                ]);
                
                break;




            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"),true);
                $errors = $this->getValidationError($data,false);

                if(!empty($errors)){
                    http_response_code(422); // unproccesable entity
                    echo json_encode(["errors"=>$errors]);
                    break;
                }

                $rows = $this->pistasGateway->update($pista,$data);


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
                echo json_encode($this->pistasGateway->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);

                $errors = $this->getValidationError($data);

                if(!empty($errors)){
                    http_response_code(422); // unprocessable entity

                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->pistasGateway->create($data);

                http_response_code(201); // elemento creado

                echo json_encode([
                    'message' =>'Pista creado',
                    'id' => $id
                ]);





                break;
            default:
                http_response_code(405); //method not allowed
                header("Allow:GET,POST"); //INFORMAR DISPONIBLES
        }
    }





    private function getValidationError(array $data , bool $is_new=true) : array{
        $errors = [];

        //-------------------- name validation -------------------------
        if ($is_new && empty($data["name"])) {

            $errors[] = "name is required";
            
            }
        //TODO


        //----------------- telefon validation -------------------

        //TODO
        //--------------------------- age validation -----------------

        
        //TODO

        //-------------- penalizado validation --------------------------

        
        //TODO



        return $errors;

    }




}