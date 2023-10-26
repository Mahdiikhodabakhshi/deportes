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
}