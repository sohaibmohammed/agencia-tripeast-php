<?php

// Conexão com o banco de dados
require_once('db_connect.php');
// Upload de imagem
require_once('fn_upload_image.php');
// Função para renderizar o pacote
require_once('./layout/package.php');

function getPackageById($connect, $id)
{
  $query = "SELECT * FROM packages WHERE id = $id";

  $execute = mysqli_query($connect, $query);

  $package = mysqli_fetch_assoc($execute);

  if (!empty($package['id'])) {
    return $package;
  }
}

// Busca todos os pacotes cadastrados no banco de dados
function getAllPackages($connect)
{
  $query = "SELECT * FROM packages";

  $execute = mysqli_query($connect, $query);

  return mysqli_fetch_all($execute, MYSQLI_ASSOC);
}

// Função para cadastrar pacote no banco de dados
function createPackage($connect)
{
  if (isset($_POST['submited_created_package_form'])) {
    $destination = mysqli_real_escape_string($connect, $_POST['destination']);
    $hotel = mysqli_real_escape_string($connect, $_POST['hotel']);
    $hotel_stars = $_POST['hotel_stars'];
    $price = mysqli_real_escape_string($connect, $_POST['price']);
    $departure_date =  date('Y-m-d', strtotime($_POST["departure_date"]));
    $date_back = date('Y-m-d', strtotime($_POST["date_back"]));
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size'];

    $_SESSION['errors'] = array();

    // Função que verifica de algum campo não foi preenchido no formulário
    $isValidInputs = (!empty($destination) and !empty($hotel) and !empty($hotel_stars) and !empty($price) and !empty($departure_date) and !empty($date_back) and !empty($image));


    // Se algum campo do formulário não for preenchido, gera um erro
    if (!$isValidInputs) {
      $_SESSION['errors'][] = "Todos os campos são obrigatórios.";
    }

    if ($imageSize > (500 * 1024)) {
      $_SESSION['errors'][] = "Imagem muito grande. Max.: 500k";
    }

    // Caso não ocorra nenhum erro, é feito o INSERTO no banco de dados.
    if (empty($_SESSION['errors'])) {
      $query = "INSERT INTO packages (destination, hotel, hotel_stars, price, departure_date, date_back, image)
        VALUES ('$destination', '$hotel', '$hotel_stars', '$price', '$departure_date', '$date_back', '$image')";

      $execute = mysqli_query($connect, $query);

      // Se der tudo certo na execução da query, retorna o "ID do último pacote cadastrado"
      // esse ID é necessário para criar o diretório onde ficará a imagem do pacote cadastrado
      if ($execute) {
        $lastPackageId =  mysqli_insert_id($connect);

        // Neste momento é feito o upload da imagem, onde é criado o dsiretório com o ID do pacote recém cadastrado no banco
        uploadImage($lastPackageId, $image_tmp_name, $image);

        return $lastPackageId;
      }
    }
  }
}

function deletePackage($connect, $id)
{
  $query = "DELETE FROM packages WHERE id = $id";

  $execute = mysqli_query($connect, $query);

  if ($execute) {
    return true;
  }
}

// Função para cadastrar pacote no banco de dados
function editPackage($connect, $id)
{
  if (isset($_POST['submited_edited_package_form'])) {
    $destination = mysqli_real_escape_string($connect, $_POST['destination']);
    $hotel = mysqli_real_escape_string($connect, $_POST['hotel']);
    $hotel_stars = $_POST['hotel_stars'];
    $price = mysqli_real_escape_string($connect, $_POST['price']);
    $departure_date =  date('Y-m-d', strtotime($_POST["departure_date"]));
    $date_back = date('Y-m-d', strtotime($_POST["date_back"]));
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size'];

    $_SESSION['errors'] = array();

    // Função que verifica de algum campo não foi preenchido no formulário
    $isValidInputs = (!empty($destination) and !empty($hotel) and !empty($hotel_stars) and !empty($price) and !empty($departure_date) and !empty($date_back) and !empty($image));


    // Se algum campo do formulário não for preenchido, gera um erro
    if (!$isValidInputs) {
      $_SESSION['errors'][] = "Todos os campos são obrigatórios.";
    }

    if ($imageSize > (500 * 1024)) {
      $_SESSION['errors'][] = "Imagem muito grande. Max.: 500k";
    }

    // Caso não ocorra nenhum erro, é feito o INSERTO no banco de dados.
    if (empty($_SESSION['errors'])) {
      $query = "UPDATE packages SET
      destination = '$destination',
      hotel = '$hotel',
      hotel_stars = '$hotel_stars',
      price = '$price',
      departure_date = '$departure_date',
      date_back = '$date_back',
      image = '$image'
      WHERE id = $id";

      $execute = mysqli_query($connect, $query);

      // Se der tudo certo na execução da query, retorna o "ID do último pacote cadastrado"
      // esse ID é necessário para criar o diretório onde ficará a imagem do pacote cadastrado
      if ($execute) {
        // $lastPackageId =  mysqli_insert_id($connect);
        $packageUdited = true;
        // Neste momento é feito o upload da imagem, onde é criado o dsiretório com o ID do pacote recém editado
        uploadImage($id, $image_tmp_name, $image);

        return $packageUdited;
      }
    }
  }
}
