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
        //todo
    }
    private function processCollectionRequest(string $method ) : void {
        switch($method){
            case 'GET':
                echo json_encode($this->pistasGateway->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);

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



}