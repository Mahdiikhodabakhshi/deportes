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
            echo json_encode(['error' => 'reserve not founded']);
            return;
        }

        switch($method){
            case "GET":
                echo json_encode($reserva);
                break;



            case "DELETE":
                $rows = $this->reservasGateway->delete($id);

                echo json_encode([
                    "message" =>"RESERVE $id deleted",
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
                    "message" => "RESERVE $id updated",
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
                $errors = $this->getValidationError($data);

                if(!empty($errors)){
                    http_response_code(422); // unprocessable entity

                    echo json_encode(["errors" => $errors]);
                    break;
                }
                    

                    
                (array) json_decode( file_get_contents("http://localhost/deportes/socios/".$data["socio"] )  ) ;
                (array) json_decode(file_get_contents("http://localhost/deportes/pistas/".$data["pista"]))  ;
                

            
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

        if(!$data ){
            $errors[] = 'data formation is not correct ';
        }else{

                //-------------------- MEMBER & COURT validation -------------------------

                if($is_new && empty($data["socio"]) ){
                    $errors[] = 'MEMBER is required';
                }
                if ( array_key_exists( "socio", $data)) {

                    if ( filter_var($data["socio"], FILTER_VALIDATE_INT) === false ) {
                    
                    $errors[] = "MEMBER must be an integer";
                    
                    }
                    
                    }
                
                if($is_new && empty($data['pista'])){
                    $errors[] = 'COURT is required';
                }
                if ( array_key_exists( "pista", $data)) {

                    if ( !is_int($data["pista"]) ) {
                    
                    $errors[] = "COURT must be an integer";
                    
                    }
                    
                    }
                


                //----------------- DATE validation -------------------

                if($is_new && empty($data['fecha'])){
                    $errors[] = 'DATE is required';
                }
                if ( array_key_exists( "fecha", $data)) {
                    $x = intval($data["fecha"]);
                    if ( $x === 0 ) {
                    
                    $errors[] = "DATE must be a string of numbers";
                    
                    }
                    
                    }
                
                //--------------------------- TIME validation -----------------


                if(array_key_exists('hora', $data)){
                    if(!is_int($data['hora'])){
                        $errors[] = 'TIME must be an INTEGER';
                    }
                    if( $data['hora'] < 0 || $data['hora'] > 24){
                        $errors[] = 'Time must be an number between 0-24';
                    }
                }
    

                //-------------- LIGHT UP validation --------------------------


                if(array_key_exists('iluminar', $data)){
                    if(!is_bool($data['iluminar'])){
                        $errors[] = 'LIGHT UP must be BOOLEAN';
                    }
                }



        }
       

        return $errors;

    }

    



}