<?php
class SociosGateway{
    private PDO $conn;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll() : Array {
        $sql = "SELECT * FROM socio";
        $stmt = $this->conn->query($sql);
        $data=[];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $row["penalizado"] = (bool)$row["penalizado"];
            $data[]=$row;
        }

        return $data;


    }

    public function create(array $data) : string {
    
        $sql = "INSERT INTO socio (nombre , telefono , edad , penalizado) 
        VALUES (:nombre , :telefono , :edad , :penalizado)";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindValue(":nombre",$data["nombre"], PDO::PARAM_STR);
        $stmt ->bindValue(":telefono",$data["telefono"], PDO::PARAM_STR);
        $stmt ->bindValue(":edad",($data["edad"] ?? 0), PDO::PARAM_INT);
        $stmt ->bindValue(":penalizado",(bool)($data["penalizado"] ?? false), PDO::PARAM_BOOL);
    
    
        $stmt ->execute();
        return $this->conn->lastInsertId();
    }


    public function get(string $id) : array | false {
        $sql = "SELECT * FROM socio WHERE id = :id";
        $stmt= $this->conn->prepare($sql);
        $stmt->bindValue(':id',$id,PDO::PARAM_INT);
        $stmt -> execute();
        $data = $stmt -> fetch(PDO::FETCH_ASSOC);

        if($data !== false){
            $data["penalizado"]=(bool)$data["penalizado"];
        }

        return $data;
    }

    public function idEnReserva(int $idSocio) : Array {
        $sql = "SELECT socio FROM reserva";
        $stmt = $this->conn->query($sql);
        $data=[];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC) ){
           if($row["socio"] == $idSocio){
            $data[]=$row;
           }
        }
      


        return $data;


    }

    public function update(array $current , array $new){
        $sql="UPDATE socio SET nombre = :nombre ,
         telefono = :telefono , edad = :edad ,
         penalizado = :penalizado WHERE id = :id";

         $stmt = $this->conn->prepare($sql);

         $stmt ->bindValue(":nombre",$new["nombre"] ?? $current["nombre"], PDO::PARAM_STR);
         $stmt ->bindValue(":telefono",$new["telefono"] ?? $current["telefono"], PDO::PARAM_STR);
         $stmt ->bindValue(":edad",$new["edad"] ?? $current["edad"], PDO::PARAM_INT);
         $stmt ->bindValue(":penalizado",$new["penalizado"] ?? $current["penalizado"], PDO::PARAM_BOOL);
     
         $stmt -> bindValue(":id" , $current["id"] , PDO::PARAM_INT);

         $stmt -> execute();
         return $stmt->rowCount() ;


    }


    public function delete (string $id) : int{
       
       $existed= $this->get($id);
       if(!$existed){
        
       }
        $sql ="DELETE FROM socio WHERE id = :id ";

        $stmt=$this->conn->prepare($sql);

        $stmt ->bindValue(":id" , $id , PDO::PARAM_INT);

        $stmt->execute();
        return  $stmt->rowCount();
    }
}