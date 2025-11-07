<?php
include_once("../utils/functions.php");
include_once("../utils/emailService.php");

checkLogin();

if (!isEmployee()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");
include_once("../utils/emailService.php");

$employee_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    // Check if already applied
    $check = $pdo->prepare("SELECT id FROM applications WHERE employee_id = :eid AND task_id = :tid");
    $check->bindParam(":eid", $employee_id);
    $check->bindParam(":tid", $task_id);
    $check->execute();
    if($check->rowCount() > 0){
        header("Location: dashboard.php?msg=Already applied.");
        exit();
    }

    // Handle resume upload
    $resume_path = null;
    if(isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK){
        $ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        $resume_name = "resume_".$employee_id."_".time().".".$ext;
        $upload_dir = "../uploads/resumes/";
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $resume_path = $upload_dir.$resume_name;
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    }

    // Insert application
    $insert = $pdo->prepare("INSERT INTO applications (employee_id, task_id, status, applied_at, resume) VALUES (:eid, :tid, 'pending', NOW(), :resume)");
    $insert->bindParam(":eid", $employee_id);
    $insert->bindParam(":tid", $task_id);
    $insert->bindParam(":resume", $resume_path);
    if($insert->execute()){
        // Optional: notify employer
        $notify = $pdo->prepare("
            SELECT u.email AS employer_email, t.title
            FROM tasks t
            JOIN employers u ON t.employer_id = u.id
            WHERE t.id = :tid
        ");
        $notify->bindParam(":tid", $task_id);
        $notify->execute();
        $info = $notify->fetch(PDO::FETCH_ASSOC);
        if($info){
            $to = $info['employer_email'];
            $subject = "New Job Application: ".$info['title'];
            $body = "An employee has applied for your job. Please check your dashboard.";
            sendEmail($to, $subject, $body);
        }

        header("Location: dashboard.php?msg=Application submitted successfully.");
        exit();
    } else {
        header("Location: dashboard.php?msg=Failed to submit.");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>
