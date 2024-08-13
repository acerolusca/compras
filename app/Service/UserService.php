<?php

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepository;
use App\Utils\View;
use App\Communication\Email;

use \App\Session\Session;
use \Exception;
use \DateTime;
use \PDO;


class UserService
{


    /**
     * Instância de UserRepository
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;




    /**
     * Construtor da classe
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }




    /**
     * Método responsável pelo login de um usuário
     *
     * @param array  $userData
     * @return void
     */
    public function login(array $userData)
    {
        try {

            $identifier = $userData["identifier"] ?? "";
            $password = $userData["password"] ?? "";

            $statement = $this->userRepository->getByIdentifier($identifier);

            if ($statement->rowCount() == 0) {
                throw new Exception("Credenciais inválidas", 401);
            }


            $user = $statement->fetchObject(User::class);


            if (!password_verify($password, $user->getPassword())) {
                throw new Exception("Credenciais inválidas", 401);
            }

            $user->login();
            
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    
    /**
     * Método responsável pelo cadastro de um usuário
     *
     * @param array $userRegisterData
     * @return string
     */
    public function register(array $userRegisterData): string
    {
        try {

            $user = new User();

            $user->setFullname($userRegisterData["registerFullname"] ?? "");

            $user->setUsername($userRegisterData["registerUsername"] ?? "");
            $this->ensureIsUnique("username", $user->getUsername(), "nome de usuário");

            $user->setEmail($userRegisterData["registerEmail"] ?? "");
            $this->ensureIsUnique("email", $user->getEmail(), "email");

            $user->setCpf($userRegisterData["registerCpf"] ?? "");
            $this->ensureIsUnique("cpf", $user->getCpf(), "CPF");

            $user->setImagePath("default-image-path.svg");
            $user->setPrivilege("editor");
            $user->setRegisterDate((new DateTime())->format("Y-m-d H:i:s"));
            $user->setFirstAccess("yes");
            $randomPassword = $user->setRandomPassword();


            $this->sendRegisterEmail(
                $user->getFullname(), 
                $user->getEmail(), 
                $user->getCpf(), 
                $randomPassword
            );


            $this->userRepository->insert($user);

            return $randomPassword;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por enviar email ao usuário com a senha de cadastro
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    private function sendRegisterEmail(string $name, string $email, string $cpf, string $password)
    {

        try {

            $body = View::render("pages/users/register-email", [
                "name" => $name,
                "email" => $email,
                "cpf" => $cpf,
                "password" => $password
            ]);


            $subject = "CADASTRO NO SISTEMA DE GERENCIAMENTO COMPRAS-MA";


            $objEmail = new Email();
            $success = $objEmail->sendEmail($email, $subject, $body);

            if (!$success) {
                throw new Exception(
                    "Problema ao enviar email de confirmação de cadastro. Por favor, tente novamente mais tarde", 
                    400
                );
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por retornar uma string (HTML) com o corpo da tabela de usuários (não administradores)
     *
     * @return string
     */
    public function getNoAdministratorUsersTableBody(): string
    {

        try {

            $statement = $this->userRepository->getAll();

            if ($statement->rowCount() == 0) {
                return "";
            }

            $usersList = "";


            while ($user = $statement->fetchObject(User::class)) {

                if($user->getPrivilege() != "administrator") {
                    $usersList .= View::render("pages/users/user-item", [
                        "imagePath" => $user->getImagePath(),
                        "fullname" => $user->getFullname(),
                        "email" => $user->getEmail(),
                        "cpf" => $user->getCpf(),
                        //"registerDate" => (new DateTime($user->getRegisterDate()))->format("d/m/Y H:i:s")
                    ]);
                }

            }

            return $usersList;
        } catch (Exception $e) {
            return "";
        }
    }



    /**
     * Método responsável por retornar um array com a informações de um usuário buscado a partir de seu CPF
     *
     * @param string $cpf
     * @return array
     */
    public function getInfo(string $cpf): ? array
    {
        try {

            if(!is_numeric($cpf)){
                throw new Exception("Parâmetro de usuário inválido.", 400);
            }

            $statement = $this->userRepository->getByCpf($cpf);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "É possível que ele tenha sido excluído por outro administrador enquanto você tentava editá-lo.", 
                    404
                );
            }

            $user = $statement->fetchObject(User::class);

            $userInfo = [];

            $userInfo["fullname"] = $user->getFullname() ?? "";
            $userInfo["username"] = $user->getUsername() ?? "";
            $userInfo["email"] = $user->getEmail() ?? "";
            $userInfo["cpf"] = $user->getCpf() ?? "";
            $userInfo["imagePath"] = $user->getImagePath() ?? "default-image-path.svg";

            return $userInfo;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável pela edição das informações de um usuário
     * @param array $userEditData
     * @return boolean
     */
    public function edit(array $userEditData): bool
    {
        try {

            //GARANTE QUE A CHAVE "editLastCpf" ESTEJA DEFINIDA EM $userEditData
            $userEditData["editLastCpf"] ??= "";

            $statement = $this->userRepository->getByCpf($userEditData["editLastCpf"]);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "É possível que ele tenha sido excluído por outro administrador enquanto você tentava editá-lo.", 
                    404
                );
            }
            
            unset($userEditData["editLastCpf"]);

            $userFound = $statement->fetchObject(User::class);

            $lastCpf = $userFound->getCpf();

            $this->setDataOnUserFound($userEditData, $userFound);

            return $this->userRepository->update($userFound, $lastCpf);

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }


    
    /**
     * Método responsável pela edição de perfil de um usuário
     * @param array $userProfileData
     * @return boolean (Indica se usuário continua logado ou não)
     */
    public function editProfile(array $userProfileData): bool {

        try {

            //GARANTE QUE A CHAVE "profileLastCpf" ESTEJA DEFINIDA EM $userProfileData
            $userProfileData["profileLastCpf"] ??= "";

            $statement = $this->userRepository->getByCpf($userProfileData["profileLastCpf"]);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "Não houve sucesso ao encontrar os seus dados. É possível que eles tenham sido excluídos do sistema.", 
                    404
                );
            }
            
            unset($userProfileData["profileLastCpf"]);

            $imageTmpName = $userProfileData["imageTmpName"];

            $userFound = $statement->fetchObject(User::class);

            $lastCpf = $userFound->getCpf();
            $lastEmail = $userFound->getEmail();
            $imageCurrentName = $userFound->getImagePath();

            if(!empty($imageTmpName)){
                $userFound->validateProfileImage($imageTmpName);
                $newImageName = $userFound->saveProfileImage($imageTmpName, $imageCurrentName);
                $userFound->setImagePath($newImageName);
                $userFound->updateSessionImagePath();
            }

            $this->setDataOnUserFound($userProfileData, $userFound);

            $this->userRepository->update($userFound, $lastCpf);

            $userFound->updateSessionFullname();
            $userFound->updateSessionUsername();
            $userFound->updateSessionEmail();
            $userFound->updateSessionCpf();
            
            if ($lastCpf != $userFound->getCpf() || $lastEmail != $userFound->getEmail()) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por editar a senha de um usuário
     *
     * @param array $editPassowrdData
     * @return boolean
     */
    public function editPassword(array $editPasswordData): bool
    {

        try {
             
            $currentPassword  = $editPasswordData["profilePassword"] ?? "";
            $newPassword = $editPasswordData["profileNewPassword"] ?? "";
            $confirmNewPassword = $editPasswordData["profileConfirmNewPassword"] ?? "";

            $this->validateNewPasswordData($currentPassword, $newPassword, $confirmNewPassword);

            $lastCpf = $editPasswordData["profileLastCpfForPassword"] ?? "";

            $statement = $this->userRepository->getByCpf($lastCpf);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "Não houve sucesso ao encontrar os seus dados. É possível que eles tenham sido excluídos do sistema.", 
                    404
                );
            }

            $userFound = $statement->fetchObject(User::class);

            if(!password_verify($currentPassword, $userFound->getPassword())){
                throw new Exception("Senha atual incorreta.", 400);
            }

            $userFound->setPassword($newPassword);

            return $this->userRepository->update($userFound, $lastCpf);


        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por redefinir a senha de um usuário no primeiro acesso
     *
     * @param array $redefineFirstAccessPasswordData
     * @return void
     */
    public function redefineFirstAccessPassword(array $redefineFirstAccessPasswordData): void
    {

        try {
            $currentPassword = "alrealdyValidatedPassword";
            $newPassword = $redefineFirstAccessPasswordData["firstAccessNewPassword"] ?? "";
            $confirmNewPassword = $redefineFirstAccessPasswordData["firstAccessConfirmNewPassword"] ?? "";

            $this->validateNewPasswordData($currentPassword, $newPassword, $confirmNewPassword);


            $cpf = (new User())->getLoggedInfo()["cpf"];

            $statement = $this->userRepository->getByCpf($cpf);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "Não houve sucesso ao encontrar os seus dados. É possível que eles tenham sido excluídos do sistema.", 
                    404
                );
            }

            $userFound = $statement->fetchObject(User::class);
            $userFound->setPassword($newPassword);
            $userFound->setFirstAccess("no");
            $this->userRepository->update($userFound, $cpf);

            $userFound->updateSessionFirstAccess();

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por validar previamente os dados para a alteração de senha (senha atual, nova senha e confirmação de nova senha)
     * @param string  $currentPassword
     * @param string  $newPassowrd
     * @param string  $confirmNewPassword
     * @return void
     */
    private function validateNewPasswordData(string $currentPassword, string $newPassowrd, string $confirmNewPassword): void {
        try {
            if($currentPassword == ""){
                throw new Exception("Informe a sua senha atual.", 400);
            }
    
            if($newPassowrd == ""){
                throw new Exception("Informe a nova senha", 400);
            }
    
            if($currentPassword == $newPassowrd){
                throw new Exception("A senha atual e a nova senha não devem coincidir", 400);
            }
    
            if($newPassowrd != $confirmNewPassword){
                throw new Exception("Verifique a confirmação de nova senha. Senhas não coincidem.", 400);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

    }


    /**
     * Método responsável por atribuir as alterações enviadas no formulário de edição ao usuário encontrado
     * @param array $userEditData
     * @param User $userFound
     * @return User
     */
    private function setDataOnUserFound(array $data, User $userFound): User {

        try{

            //LÓGICA PARA VERIFICAR O ENVIO DE IMAGEM
            if(array_key_exists("imageTmpName", $data)){
                $changesCounter = (!empty($data["imageTmpName"])) ? 1 : 0;
                unset($data["imageTmpName"]);
            } else {
                $changesCounter = 0;
            }


            //EXTRAINDO O PREFIXO DAS CHAVES DO ARRAY DE DADOS
            $firstKey = array_key_first($data);
            $upperLetters = implode("", range("A", "Z"));
            $keyFirstUpperLetterPosition = strcspn($firstKey, $upperLetters);
            $prefix = substr($firstKey, 0, $keyFirstUpperLetterPosition);

            $data = $this->removePrefixFromArrayKeys($prefix, $data);


            //LABELS DAS PROPRIEDADES QUE DEVEM SER ÙNICAS
            $uniquePropertyLabels = [
                "username" => "nome de usuário",
                "email" => "email",
                "cpf" => "CPF"
            ];
    

    
            //PERCORRE O ARRAY COM OS DADOS RECÉM ENVIADOS NO FORMULÁRIO DE EDIÇÃO
            foreach ($data as $property => $value) {
    
                //CAPTURA O VALOR ATUAL DE DETERMINADA PROPRIEDADE DO USUÁRIO
                $currentValue = $this->callUserGetterMethodByProperty($userFound, $property);
    
                //SE O VALOR ATUAL FOR DIFERENTE DO CORRESPONDENTE NO ARRAY COM OS DADOS DO FORMULÁRIO DE EDIÇÃO
                if($currentValue != $value) {
    
                    //ATRIBUI O VALOR ENVIADO NO FORMULÁRIO À PROPRIEDADE CORRESPONDENTE
                    $this->callUserSetterMethodByProperty($userFound, $property, $value);
                    $changesCounter ++;
    
                    //SE O VALOR DO DADO EM QUESTÃO DEVE SER ÚNICO
                    if(in_array($property, array_keys($uniquePropertyLabels))) {
    
                        //RECUPERA A LABEL DO DADO
                        $label = $uniquePropertyLabels[$property];
    
                        //CHAMA O MÉTODO QUE GARANTE A UNICIDADE 
                        $this->ensureIsUnique($property, $value, $label);
                    }
                }
    
            } 
    
            if($changesCounter == 0) {
                throw new Exception("Nenhuma alteração foi detectada.");
            }
            
            return $userFound;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }


    }



    /**
     * Método responsável por chamar Getter específico da classe User de acordo com a propriedade
     * @param User $user
     * @param string $property
     * @return mixed
     */
    private function callUserGetterMethodByProperty(User $user, string $property): mixed {
        try{
            $getterMethod = "get". ucfirst($property);
            if (!method_exists($user, $getterMethod)) {
                throw new Exception("Método $getterMethod não foi definido!" , 400);     
            }
            return $user->$getterMethod();
        } catch (Exception $e) {    
            throw new Exception($e->getMessage(), $e->getCode());
        } 
    }



    /**
     * Método responsável por chamar Setter específico da classe User de acordo com a propriedade
     * @param User $user
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    private function callUserSetterMethodByProperty(User $user, string $property, mixed $value): mixed {
        try{
            $setterMethod = "set". ucfirst($property);
            if (!method_exists($user, $setterMethod)) {
                throw new Exception("Método $setterMethod não foi definido!" , 400);     
            }
            return $user->$setterMethod($value);
        } catch (Exception $e) {    
            throw new Exception($e->getMessage(), $e->getCode());
        } 
    }


    /**
     * 
     * Método responsável por verificar garantir que não haja duplicatas para campos que dvem ser únicos no banco
     * @param string $key
     * @param mixed $value
     * @param string $label
     * @return void
     */
    private function ensureIsUnique(string $key, mixed $value, string $label): void {
        try {

            $isUnique = $this->userRepository->isUnique($key, $value);

            if (!$isUnique) {
                throw new Exception("Valor de <strong>$label</strong> já existe.", 400);
            }

        } catch(Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por remover um prefixo das chaves de um array
     * @param array $editArray
     * @return array
     */
    private function removePrefixFromArrayKeys(string $prefix, array $array): array {
        $newEditArray = [];
        foreach ($array as $currentKey => $value) {
            $newKey = lcfirst(str_replace($prefix, "", $currentKey));
            $newEditArray[$newKey] = $value;
        }

        return $newEditArray;
    }



    /**
     * Método responsável por atribuir uma nova senha gerada randomicamente ao usuário
     * @param string $cpf
     * @return string
     */
    public function generateNewPassword(string $cpf): string {

        if(!is_numeric($cpf)){
            throw new Exception("Parâmetro de usuário inválido.", 400);
        }

        $statement = $this->userRepository->getByCpf($cpf);

        if ($statement->rowCount() == 0) {
            throw new Exception(
                "É possível que o usuário tenha sido excluído por outro administrador enquanto você tentava gerar uma nova senha para ele.", 
                404
            );
        }

        $userFound = $statement->fetchObject(User::class);

        $newPassword = $userFound->setRandomPassword();
        $userFound->setFirstAccess("yes");

        $this->userRepository->update($userFound, $cpf);

        return $newPassword;

    }




    /**
     * Método responsável por deletar um usuário
     *
     * @param string $cpf
     * @return void
     */
    public function delete(string $cpf)
    {
        try {

            if(!is_numeric($cpf)){
                throw new Exception("Parâmetro de usuário inválido.", 400);
            }

            $statement = $this->userRepository->getByCpf($cpf);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "É possível que ele tenha sido excluído por outro administrador enquanto você tentava excluí-lo.", 
                    404
                );
            }


            $this->userRepository->delete($cpf);

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    
}
