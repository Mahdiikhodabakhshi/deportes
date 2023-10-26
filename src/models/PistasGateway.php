<?php
class PistasGateway{
    private PDO $conn;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll() : Array {
        $sql = "SELECT * FROM pista";
        $stmt = $this->conn->query($sql);
        $data=[];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $row["disponible"] = (bool)$row["disponible"];
            $data[]=$row;
        }

        return $data;


    }

    public function create(array $data): string{
        $sql = "INSERT INTO pista(nombre , tipo , max_jugadores , disponible) 
        VALUES (:nombre , :tipo , :max_jugadores , :disponible) ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":nombre", $data["nombre"], PDO::PARAM_STR);
        $stmt->bindValue(":tipo", $data["tipo"], PDO::PARAM_STR);
        $stmt->bindValue(":max_jugadores", $data["max_jugadores"] ?? 2, PDO::PARAM_INT);
        $stmt->bindValue(":disponible",(bool) ($data["disponible"] ?? false), PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();



    }
}