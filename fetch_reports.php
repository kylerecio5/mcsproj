<?php
// Database connection
$host = "localhost";
$dbname = "tms";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    if (isset($_GET['reportType']) && !empty($_GET['reportType'])) {
        $reportType = $_GET['reportType'];

        if ($reportType == 'MonthlyDues') {

            $sql = "SELECT 
            'Monthly Dues' as TransactionType,
            CONCAT(r.F_name, ' ', r.M_name, ' ', r.L_name) as Name,
            r.MemberType,
            md.StreetLight,
            md.Amount,
            h.Date,
            h.Month
            FROM `tbl_monthly_dues` md
            JOIN tbl_residents r
            ON r.Resident_ID = md.Resident
            JOIN tbl_history h
            ON h.Resident_ID = r.Resident_ID";
            $result = $conn->query($sql);

            $data = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        "TransactionType" => $row["TransactionType"],
                        "Name" => $row["Name"],
                        "MemberType" => $row["MemberType"],
                        "StreetLight" => $row["StreetLight"],
                        "Amount" => $row["Amount"],
                        "Date" => $row["Date"],
                        "Month" => $row["Month"]
                    ];
                }
            }
            echo json_encode($data);
        } else if ($reportType == 'Stickers') {
            $sql = "SELECT 
            'Stickers' as TransactionType,
            s.NAME as ResidentName,
            s.PHONE_NO as PhoneNo,
            s.DATE as Date,
            s.VehicleType,
            s.PlateNum,
            s.StickerNum,
            s.Amount
            FROM tbl_stickers s"; 

            $result = $conn->query($sql);

            $data = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        "TransactionType" => $row["TransactionType"],
                        "ResidentName" => $row["ResidentName"],
                        "PhoneNo" => $row["PhoneNo"],
                        "Date" => $row["Date"],
                        "VehicleType" => $row["VehicleType"],
                        "PlateNum" => $row["PlateNum"],
                        "StickerNum" => $row["StickerNum"],
                        "Amount" => $row["Amount"]
                    ];
                }
            }
            echo json_encode($data);

        } else if ($reportType == 'CPermit') {
            $sql = "SELECT 
            'C Permit' as TransactionType,
            c.Name as ResidentName,
            c.PhoneNo,
            c.BuildingType,
            c.PermitNum,
            c.PermitDate,
            c.Block,
            c.Lot,
            c.Street,
            c.Amount
            FROM tbl_construction_permits c"; 

            $result = $conn->query($sql);

            $data = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        "TransactionType" => $row["TransactionType"],
                        "ResidentName" => $row["ResidentName"],
                        "PhoneNo" => $row["PhoneNo"],
                        "BuildingType" => $row["BuildingType"],
                        "PermitNum" => $row["PermitNum"],
                        "PermitDate" => $row["PermitDate"],
                        "Block" => $row["Block"],
                        "Lot" => $row["Lot"],
                        "Street" => $row["Street"],
                        "Amount" => $row["Amount"]
                    ];
                }
            }
            echo json_encode($data);

        } else if ($reportType == 'Parking') {
            $sql = "SELECT 
            'Parking' as TransactionType,
            p.NAME as ResidentName,
            p.PHONE_NO as PhoneNo,
            p.VEHICLE_TYPE as VehicleType,
            p.PLATE_NO as PlateNum,
            p.PARKING_TYPE as ParkingType,
            p.AMOUNT as Amount,
            p.DATE as Date
            FROM tbl_parking p"; 

            $result = $conn->query($sql);

            $data = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        "TransactionType" => $row["TransactionType"],
                        "ResidentName" => $row["ResidentName"],
                        "PhoneNo" => $row["PhoneNo"],
                        "VehicleType" => $row["VehicleType"],
                        "PlateNum" => $row["PlateNum"],
                        "ParkingType" => $row["ParkingType"],
                        "Amount" => $row["Amount"],
                        "Date" => $row["Date"]
                    ];
                }
            }
            echo json_encode($data);
        }

        $conn->close();
    } else {

        echo 'error';
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()]);
}
?>
