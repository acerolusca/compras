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








  //------------------------------------ Geterss and Setters ----------------------------------------//
 


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
    $this->fullname = $fullname;
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
   * Método responsável por atribuir um cpf ao usuário
   * @param string $cpf
   * @return void
   */
  public function setCpf(string $cpf): void
  {
    $this->cpf = $cpf;
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
    $this->username = $username;
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
    $this->email = $email;
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
    $this->password = $password;
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
   * Método responsável por atribuir um caminho padrão de imagem de perfil ao usuário
   * @return void
   */
  public function setDefaultImagePath(): void
  {
    $this->imagePath = "default-image-path.svg";
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
    $this->privilege = $privilege;
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
    try{

      if($this->isLogged()){
        return $_SESSION["user"];
      }

      throw new Exception("Sessão Expirada", 403);

    } catch (Exception $e){
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
      if($this->isLogged()){
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
    } catch (Exception $e){
      throw new Exception($e->getMessage(), $e->getCode());
    }



  }


  /**
   * Método responsável por retornar o privilégio do usuário
   * @return string
   */
  public function getSessionPrivilege(): string {
    try{
      return $this->getLoggedInfo()["privilege"];
    } catch(Exception $e){
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




}