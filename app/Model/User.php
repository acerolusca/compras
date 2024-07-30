<?php

namespace App\Model;

use \App\Session\UserSession;
use \Exception;


class User
{

  //------------------------------------------------ Attributes --------------------------------------------//  


  /**
   * Id do usuário
   * @var integer
   */
  private ?int $id;


  /**
   * Nome completo do usuário
   * @var string
   */
  private string $fullname;


  /**
   * Cpf do usuário
   * @var string
   */
  private string $cpf;


  /**
   * Nome de usuário do usuário
   * @var string
   */
  private string $username;


  /**
   * Email do usuário
   * @var string
   */
  private string $email;


  /**
   * Senha do usuário
   * @var string
   */
  private string $password;


  /**
   * Caminho da imagem de perfil do usuário
   * @var string
   */
  private string $imagePath;



  /**
   * Privilégio do usuário (editor ou administrador)
   * @var string
   */
  private string $privilege;



  /**
   * Data de registro do usuário
   * @var string
   */
  private string $registerDate;








  //------------------------------------ Getters and Setters ----------------------------------------//



  /**
   * Método responsável por retornar o ID do usuário
   * @return integer
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Método responsável por atribuir um ID ao usuário
   * @param integer $id
   * @return void
   */
  public function setId(int $id): void
  {
    $this->id = $id;
  }





  /**
   * Método responsável por retornar o nome completo do usuário
   *
   * @return string
   */
  public function getFullname(): string
  {
    return $this->fullname;
  }

  /**
   * Método responsável por atribuir um nome completo ao usuário
   * @param string $fullname
   * @return void
   */
  public function setFullname(string $fullname): void
  {
    try {
      if(trim($fullname) == ""){
        throw new Exception("<strong>Nome completo</strong> é obrigatório.", 400);
      }

      $fullnameRegex = "/[^\sa-zA-ZàáâãéêíóôõúÀÁÂÃÉÊÍÓÔÕÚçÇ']|\s{2,}|'{2,}|^\s+|^'/";
      if (preg_match($fullnameRegex, $fullname)) {
        throw new Exception("<strong>Nome completo </strong> inválido.", 400);
      }

      if (strlen($fullname) <= 3) {
        throw new Exception("É necessário que o <strong>nome completo</strong> tenha pelo menos 3 caracteres.", 400);
      }

      if (strlen($fullname) > 255) {
        throw new Exception("O <strong>nome completo</strong> não deve ultrapassar 255 caracteres.", 400);
      }

      $this->fullname = ucwords(mb_strtolower($fullname));

    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }




  /**
   * Método responsável por retornar o cpf do usuário
   * @return string
   */
  public function getCpf(): string
  {
    return $this->cpf;
  }

  /**
   * Método responsável por atribuir um CPF ao usuário
   * @param string $cpf
   * @return void
   */
  public function setCpf(string $cpf): void
  {
    try {

      if(trim($cpf) == ""){
        throw new Exception("<strong>CPF</strong> é obrigatório.", 400);
      }

      if (!$this->validateCpfDigits($cpf)) {
        throw new Exception("<strong>CPF</strong> inválido", 400);
      }

      $this->cpf = $cpf;

    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }




  /**
   * Método responsável por retornar o nome de usuário do usuário
   *
   * @return string
   */
  public function getUsername(): string
  {
    return $this->username;
  }

  /**
   * Método responsável por atribuir um nome de usuário ao usuário
   * @param string $username
   * @return void
   */
  public function setUsername(string $username): void
  {
    try {

      if(trim($username) == ""){
        throw new Exception("<strong>Nome de usuário</strong> é obrigatório.", 400);
      }

  
      $usernameRegex = "/^(?=.{5,20}$)(?![_.])(?!.*[_.]{2})[a-z0-9._]+(?<![_.])$/";

      if (!preg_match($usernameRegex, $username)) {
        throw new Exception(
          '
              <p class="text-left"><strong>Nome de usuário</strong> inválido! As seguintes regras devem ser obedecidas:</p>
              <ul class="text-left">
                <li >Deve ter entre 5 e 20 caracteres;</li>
                <li>Pode conter apenas letras minúsculas (a-z), dígitos (0-9), ponto (.) e underscore (_);</li>
                <li>Não pode começar ou terminar com ponto ou underscore e nem conter esses caracteres de forma consecutiva.</li>
              </ul>
            ',
          400
        );
      }

      $this->username = $username;

    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }



  }




  /**
   * Método responsável por retornar o email do usuário
   *
   * @return string
   */
  public function getEmail(): string
  {
    return $this->email;
  }

  /**
   * Método responsável por atribuir um email ao usuário
   * @param string $email
   * @return void
   */
  public function setEmail(string $email): void
  {
    try {

      if(trim($email) == ""){
        throw new Exception("<strong>Email</strong> é obrigatório.", 400);
      }
      
      $emailRegex = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z]{2,})+$/";

      if (!preg_match($emailRegex, $email)) {
        throw new Exception("<strong>Email</strong> inválido.", 400);
      }

      $this->email = $email;

    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }





  /**
   * Método responsável por retornar a senha do usuário
   *
   * @return string
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  /**
   * Método responsável por atribuir uma senha ao usuário
   * @param string $password
   * @return void
   */
  public function setPassword(string $password): void
  {
    try {
      $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\.])[A-Za-z\d@$!%*?&\.]{8,}$/";

      if (!preg_match($passwordRegex, $password)) {
        throw new Exception(
          '
              <p class="text-left"><strong>Senha</strong> não é forte o suficiente. As seguintes regras devem ser obedecidas:</p>
              <ul class="text-left">
                <li>Deve ter no mínimo 8 caracteres;</li>
                <li>Pelo menos 1 letra minúscula;</li>
                <li>Pelo menos 1 letra maiúscula;</li>
                <li>Pelo menos 1 dígito (0-9);</li>
                <li>Pelo menos 1 caractere especial dentre <strong>@$!%*?&\.</strong>.</li>
              </ul>
            ',
          400
        );
      }

      $this->password = password_hash($password, PASSWORD_ARGON2ID);
      
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }

  /**
   * Método responsável por atribuir uma senha randômica ao usuário
   * @return string
   */
  public function setRandomPassword(): string
  {
    $randomPassword = $this->generateRandomString(32);
    $this->password = password_hash($randomPassword, PASSWORD_ARGON2ID);
    return $randomPassword;
  }





  /**
   * Método responsável por retornar o caminho da imagem de perfil do usuário
   *
   * @return string
   */
  public function getImagePath(): string
  {
    return $this->imagePath;
  }

  /**
   * Método responsável por atribuir um caminho de imagem de perfil ao usuário
   * @param string $imagePath
   * @return void
   */
  public function setImagePath(string $imagePath): void
  {
    $this->imagePath = $imagePath;
  }



  /**
   * Método responsável por retornar o privilégio do usuário
   *
   * @return string
   */
  public function getPrivilege(): string
  {
    return $this->privilege;
  }

  /**
   * Método responsável por atribuir um privilégio ao usuário
   * @param string $privilge
   * @return void
   */
  public function setPrivilege(string $privilege): void
  {
    try {
      $validPrivileges = ["editor", "administrator"];

      if (!in_array($privilege, $validPrivileges)) {
        throw new Exception("Privilégio de usuário inválido", 400);
      }


      $this->privilege = $privilege;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }






  /**
   * Método responsável por retornar a data de registro do usuário
   * @param string $registerDate
   * @return void
   */
  public function getRegisterDate(): string
  {
    return $this->registerDate;
  }

  /**
   * Método responsável por atribuir uma data de registro ao usuário
   * @param string $registerDate
   * @return void
   */
  public function setRegisterDate(string $registerDate): void
  {
    $this->registerDate = $registerDate;
  }







  //-------------------------------------------- Session Methods ---------------------------------------------//


  /**
   * Método responsável pelo login do usuário no sistema
   * @return void
   */
  public function login(): void
  {
    (new UserSession())->init();

    $_SESSION["user"] = [
      "id" => $this->id,
      "fullname" => $this->fullname,
      "cpf" => $this->cpf,
      "username" => $this->username,
      "email" => $this->email,
      "imagePath" => $this->imagePath,
      "privilege" => $this->privilege,
      "logged" => true
    ];
  }


  /**
   * Método que verifica se o usuário está logado
   *
   * @return boolean
   */
  public function isLogged()
  {
    (new UserSession())->init();
    return isset($_SESSION["user"]["logged"]) && $_SESSION["user"]["logged"] == true;
  }



  /**
   * Método responsável por retonar as informações de sessão do usuário
   * @return array
   */
  public function getLoggedInfo()
  {
    try {

      if ($this->isLogged()) {
        return $_SESSION["user"];
      }

      throw new Exception("Sessão Expirada", 403);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }



  /**
   * Método responsável por setar as informações de sessão usuário
   * @return void
   */
  public function setLoggedInfo()
  {

    try {
      if ($this->isLogged()) {
        $this->id = $_SESSION["user"]["id"] ?? "";
        $this->fullname = $_SESSION["user"]["fullname"] ?? "";
        $this->cpf = $_SESSION["user"]["cpf"] ?? "";
        $this->username = $_SESSION["user"]["username"] ?? "";
        $this->email = $_SESSION["user"]["email"] ?? "";
        $this->imagePath = $_SESSION["user"]["imagePath"] ?? "";
        $this->privilege = $_SESSION["user"]["privilege"] ?? "";
      } else {
        throw new Exception("Sessão Expirada", 403);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }



  /**
   * Método responsável por atualizar o nome completo nas informações de sessão do usuário
   * @return void
   */
  public function updateSessionFullname()
  {

    try {
      if ($this->isLogged()) {
        $_SESSION["user"]["fullname"] = $this->fullname;
      } else {
        throw new Exception("Sessão Expirada", 403);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }




  /**
   * Método responsável por atualizar o cpf nas informações de sessão do usuário
   * @return void
   */
  public function updateSessionCpf()
  {

    try {
      if ($this->isLogged()) {
        $_SESSION["user"]["cpf"] = $this->cpf;
      } else {
        throw new Exception("Sessão Expirada", 403);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }




  /**
   * Método responsável por atualizar o nome de usuário nas informações de sessão do usuário
   * @return void
   */
  public function updateSessionUsername()
  {

    try {
      if ($this->isLogged()) {
        $_SESSION["user"]["username"] = $this->username;
      } else {
        throw new Exception("Sessão Expirada", 403);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }




  /**
   * Método responsável por atualizar o email nas informações de sessão do usuário
   * @return void
   */
  public function updateSessionEmail()
  {

    try {
      if ($this->isLogged()) {
        $_SESSION["user"]["email"] = $this->email;
      } else {
        throw new Exception("Sessão Expirada", 403);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }




  /**
   * Método responsável por atualizar o caminho da imagem de perfil nas informações de sessão do usuário
   * @return void
   */
  public function updateSessionImagePath()
  {

    try {
      if ($this->isLogged()) {
        $_SESSION["user"]["imagePath"] = $this->imagePath;
      } else {
        throw new Exception("Sessão Expirada", 403);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }




  /**
   * Método responsável por retornar o privilégio do usuário
   * @return string
   */
  public function getSessionPrivilege(): string
  {
    try {
      return $this->getLoggedInfo()["privilege"];
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }



  /**
   * Método responsável por remover as variáveis e destruir a sessão do usuário, deslogando-o
   * @return void
   */
  public function logout()
  {
    (new UserSession())->logout();
  }




  //-------------------------------------------- Utils Methods ---------------------------------------------//


  /**
   * Método responsável por gerar uma string randômica 
   *
   * @param integer $length
   * @return string
   */
  private function generateRandomString(int $length = 64): string
  {
    $length = ($length < 4) ? 4 : $length;
    return bin2hex(random_bytes(($length - ($length % 2)) / 2));
  }



  /**
   * Método responsável por validar CPF, verificando a quantidade de caracteres e a observância das regras que o torna válido. 
   * @param string $cpf
   * @return boolean
   */
  private function validateCpfDigits(string $cpf): bool
  {
    // VERIFICA O FORMATO DO CPF
    if (!preg_match("/[0-9]{11}/", $cpf)) {
      return false;
    }

    // GARANTE QUE O CPF NÃO TENHA TODOS OS DIGITOS IGUAIS
    if (str_replace($cpf[0], "", $cpf) == "") {
      return false;
    }

    $cpfSequence = substr($cpf, 0, 9);
    $firstDigit = $this->calculateCpfDigits($cpfSequence);

    $secondSequence = $cpfSequence . $firstDigit;
    $secondSequence = substr($secondSequence, 1);
    $secondDigit = $this->calculateCpfDigits($secondSequence);

    $cpfCheckDigits = substr($cpf, -2);

    return  $cpfCheckDigits == $firstDigit . $secondDigit;
  }



  /**
   * Método responsável por calcular os dígitos de verificação de um CPF a partir dos dígitos precedentes
   * @param string $parcialCpf
   * @return string
   */
  private function calculateCpfDigits(string $parcialCpf): string
  {
    $sum = 0;
    $j = 2;
    for ($i = 8; $i >= 0; $i--) {
      $sum += intval($parcialCpf[$i]) * $j;
      $j++;
    }
    $rest = $sum % 11;
    $digit = ($rest < 2) ? 0 : (11 - $rest);
    return strval($digit);
  }



  /**
   * Método responsável por validar a foto de perfil do usuário
   * @param string $tmpName
   * @return void
   */
  public function validateProfileImage(string $tmpName)
  {

    $validTypes = ["image/jpeg", "image/png", "image/webp", "image/svg+xml"];

    try {

      if (!file_exists($tmpName)) {
        throw new Exception("Problema ao carregar </strong>foto de perfil<strong>.", 400);
      }

      if (filesize($tmpName) > 5000000) {
        throw new Exception("Tamanho máximo de 5MB excedido para <strong>foto de perfil</strong>.", 400);
      }

      if (!in_array(mime_content_type($tmpName), $validTypes)) {
        throw new Exception("Tipo de arquivo inválido para <strong>foto de perfil</strong>.", 400);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }



  /**
   * Método responsável por salvar a imagem de perfil do usuário, retornando o nome do arquivo
   * @param string $tmpName
   * @param string $currentName
   * @return string
   */
  public function saveProfileImage(string $tmpName, string $currentName): string
  {

    $path = __DIR__ . "/../../resources/images/users";

    try {

      //EXCLUI A IMAGEM ATUAL, SE EXISTIR
      if ($currentName != "default-image-path.svg" && file_exists("$path/$currentName")) {
        unlink("$path/$currentName");
      }
      //GERA UM NOME RANDÔMICO PARA A IMAGEM
      $newName = $this->generateRandomString(64);


      //SETA A EXTENSÃO DA IMAGEM DE ACORDO COM O MIME TYPE
      $validTypes = ["image/jpeg" => "jpeg", "image/png" => "png", "image/webp" => "webp", "image/svg+xml" => "svg"];
      $mimeType = mime_content_type($tmpName);
      $extension = $validTypes[$mimeType];

      //CONCATENA O NOME ALEATÓRIO À EXTENSÃO
      $newName .= ".$extension";

      if(!move_uploaded_file($tmpName, "$path/$newName")){
        throw new Exception("Problema ao salvar <strong>foto de perfil</strong>", 400);
      };

      return $newName;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode());
    }
  }
}
