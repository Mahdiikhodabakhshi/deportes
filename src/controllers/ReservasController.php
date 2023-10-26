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
        //todo
    }
    private function processCollectionRequest(string $method ) : void {
        switch($method){
            case 'GET':
                echo json_encode($this->reservasGateway->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);

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



}