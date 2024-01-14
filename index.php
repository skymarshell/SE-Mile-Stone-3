<?php
session_start();
// เข้าถึง database
$host = "localhost";
$db = "test";
$user = "root";
$password = "";

// เชื่อมต่อ
try
{
    // เชื่อมต่อฐานข้อมูล
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //echo "Connected successfully";
}
// ถ้าเชื่อมต่อไม่ได้ (เช่น ใส่ชื่อฐานข้อมูลผิด)
catch (PDOException $e)
{
    echo "Error: " . " " . $e->getMessage();
}
?>


<?php
// Insert
if (isset($_POST['btnInsert']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    // check data ว่ามี username นี้แล้วรึปาว
    $sql = "SELECT count(*) FROM admin WHERE username = :u";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":u", $username);
    $stmt->execute();
    $number_of_rows = $stmt->fetchColumn();
    if ($number_of_rows == 1)
    {
        $_SESSION['alert'] = "Duplicate username";
        header("Location: index.php");
        exit();
    }
    // ถ้าไม่เคยมีก็ insert
    $sql = "INSERT INTO admin (username,password,first_name) VALUES (:u,:p,:f)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":u", $username);
    $stmt->bindParam(":p", $password);
    $stmt->bindParam(":f", $firstname);
    $stmt->execute();
    if ($stmt)
    {
        $_SESSION['alert'] = "username $username inserted ";
        header("Location: index.php");
        exit();
    }
    else
    {
        $_SESSION['alert'] = "Error";
        header("Location: index.php");
        exit();
    }
}

// Delete
if (isset($_GET['delete']))
{
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM admin WHERE id=:delete_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":delete_id", $delete_id);
    $stmt->execute();
    if ($stmt)
    {
        $_SESSION['alert'] = "id $delete_id deleted ";
        header("Location: index.php");
        exit();
    }
    else
    {
        $_SESSION['alert'] = "Error";
        header("Location: index.php");
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Hello, world!</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    </head>
</head>

<body class=" bg-info">
    <?php
    if (isset($_GET['edit']))
    {
        $id = $_GET['edit'];
        $sql = "SELECT * FROM admin WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch();
        $username = $row['username'];
        $password = $row['password'];
        $firstname = $row['first_name'];
    ?>
        <div class="edit position-absolute w-100 bg-secondary d-flex justify-content-center align-content-center " style="top: 180px;">
            <form class="" method="POST">
                <h1>Edit ID <?php echo $id ?></h1>
                <div class="form-group mt-3">
                    <label for="">Username</label>
                    <input readonly value="<?php echo $username ?>" type="text" name="username" class="form-control" placeholder="Enter username">
                </div>
                <div class="form-group mt-3">
                    <label for="">Password</label>
                    <input required value="<?php echo $password ?>" type="text" name="password" class="form-control" placeholder="Enter password">
                </div>
                <div class="form-group mt-3">
                    <label for="">First name</label>
                    <input required value="<?php echo $firstname ?>" type="text" name="firstname" class="form-control" placeholder="Enter first name">
                </div>
                <button type="button" onclick="closeForm()" name="close" class="btn btn-danger mt-3">Close</button>
                <button type="submit" name="btnEdit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>


    <?php
        if (isset($_POST['btnEdit']))
        {
            $password = $_POST['password'];
            $fname = $_POST['firstname'];
            $sql = "UPDATE admin SET password=:p ,first_name=:f WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":p", $password);
            $stmt->bindParam(":f", $fname);
            $stmt->execute();
            if ($stmt)
            {
                $_SESSION['alert'] = "ID $id Data updated";
                header("Location: index.php");
                exit();
            }
        }
    }
    ?>


    <div class="error-log" role="alert">
        <?php
        if (isset($_SESSION["alert"]))
        {
        ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>
                    <?php echo $_SESSION["alert"];
                    unset($_SESSION["alert"]); ?>
                </strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
        }
        ?>
    </div>



    <table class="table table-striped table-hover">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>First name</th>
            <th>Delele</th>
            <th>Edit</th>
        </tr>
        <?php
        // การดึงข้อมูล query
        $sql = "SELECT * FROM admin";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        // ดึงต่อละ row มา show เป็นตาราง
        while ($row = $stmt->fetch())
        {
        ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['password']; ?></td>
                <td><?php echo $row['first_name']; ?></td>
                <td><a onclick="return confirm('Delete ID <?php echo $row['id'] ?>?')" href="?delete=<?php echo $row['id'] ?>"><button class=" btn btn-danger">Delete</button></a></td>
                <td><a href="?edit=<?php echo $row['id'] ?>"><button class=" btn btn-success">Edit</button></a></td>
            </tr>
        <?php
        }
        ?>
    </table>


    <div class="d-flex justify-content-center align-items-center">
        <form class="" method="POST">
            <h1>Insert admin</h1>
            <div class="form-group mt-3">
                <label for="">Username</label>
                <input required type="text" name="username" class="form-control" placeholder="Enter username">
            </div>
            <div class="form-group mt-3">
                <label for="">Password</label>
                <input required type="text" name="password" class="form-control" placeholder="Enter password">
            </div>
            <div class="form-group mt-3">
                <label for="">First name</label>
                <input required type="text" name="firstname" class="form-control" placeholder="Enter first name">
            </div>
            <button type="submit" name="btnInsert" class="btn btn-primary mt-3">Submit</button>
        </form>
    </div>


    <script>
        function closeForm() {
            window.location.href = 'index.php';
        }
    </script>
</body>


</html>