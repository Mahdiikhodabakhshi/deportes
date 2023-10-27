<?php

class ReservasController{


    public function __construct(private ReservasGateway $reservasGateway){}


    public function processRequest(string $method , ?string $id):void 
    {
        if($id){
            $this->processResurcesRequest($method , $id);
        }else{
            $this->processCollectionRequest($method);
        }
    }

    private function processResurcesRequest(string $method , string $id) : void {
        $reserva = $this->reservasGateway->get($id);

        if(!$reserva){
            http_response_code(404); // not found
            echo json_encode(['error' => 'reserva not founded']);
            return;
        }

        switch($method){
            case "GET":
                echo json_encode($reserva);
                break;



            case "DELETE":
                $rows = $this->reservasGateway->delete($id);

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

                $rows = $this->reservasGateway->update($reserva,$data);


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
                echo json_encode($this->reservasGateway->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);

               // $errors = $this->getValidationError($data);

                if(!empty($errors)){
                    http_response_code(422); // unprocessable entity

                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->reservasGateway->create($data);

                http_response_code(201); // elemento creado

                echo json_encode([
                    'message' =>'Reserva creado',
                    'id' => $id
                ]);
                break;
            default:
                http_response_code(405); //method not allowed
                header("Allow:GET,POST"); //INFORMAR DISPONIBLES
        }
    }


    
    private function getValidationError(array $data ,bool $is_new = true) : array{
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