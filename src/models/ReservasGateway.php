<?php
class ReservasGateway{
    private PDO $conn;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll() : Array {
        $sql = "SELECT * FROM reserva";
        $stmt = $this->conn->query($sql);
        $data=[];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $row["iluminar"] = (bool)$row["iluminar"];
            $data[]=$row;
        }

        return $data;


    }


    public function create(array $data) : string {
        $sql = "INSERT INTO reserva (socio , pista , fecha , hora , iluminar) 
        VALUES (:socio , :pista , :fecha , :hora , :iluminar)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":socio", $data["socio"], PDO::PARAM_INT);
        $stmt->bindValue(":pista", $data["pista"], PDO::PARAM_INT);
        $stmt->bindValue(":fecha", $data["fecha"], PDO::PARAM_STR);
        $stmt->bindValue(":hora", ($data["hora"] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(":iluminar", (bool)($data["iluminar"] ?? false), PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();






    }
}