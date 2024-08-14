<?php

namespace App\Model;

use \Exception;
use \DateTime;
use \DOMDocument;
use \DOMXPath;


class News
{

    //------------------------------------------------ Attributes --------------------------------------------//  


    /**
     * Id da notícia
     * @var integer
     */
    private ?int $id;


    /**
     * Título da notícia
     * @var string
     */
    private string $title;


    /**
     * Resumo da notícia
     * @var string
     */
    private string $summary;


    /**
     * Corpo da notícia
     * @var string
     */
    private string $body;


    /**
     * Caminho da imagem da notícia
     * @var string
     */
    private string $imagePath;


    /**
     * Autor da notícia
     * @var string
     */
    private string $author;


    /**
     * Data de registro da notícia
     * @var string
     */
    private string $registerDate;


    /**
     * Data de agendamento da notícia
     * @var string
     */
    private string $schedulingDate;


    /**
     * Destaque da notícia (yes ou no)
     * @var string
     */
    private string $highlighted;


    /**
     * Visibilidade da notícia (yes ou no)
     * @var string
     */
    private string $visible;






    //------------------------------------ Getters and Setters ----------------------------------------//



    /**
     * Método responsável por retornar o ID da notícia
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Método responsável por atribuir um ID à notícia
     * @param integer $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }





    /**
     * Método responsável por retornar o título da notícia
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Método responsável por atribuir um título à notícia
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        try {

            if (trim($title) == "") {
                throw new Exception("<strong>Título da notícia</strong> é obrigatório.", 400);
            }

            $titleRegex = "/[^a-zA-ZàáâãéêíóôõúÀÁÂÃÉÊÍÓÔÕÚçÇ0-9\sª°º'\",-._@#$%()]/";

            if (preg_match($titleRegex, $title)) {
                throw new Exception("<strong>Título da notícia</strong> contém caracteres inválidos.", 400);
            }

            $titleRegex = "/^[^a-zA-ZàáâãéêíóôõúÀÁÂÃÉÊÍÓÔÕÚçÇ]/";
            if (preg_match($titleRegex, $title)) {
                throw new Exception("<strong>Título da notícia</strong> não deve iniciar com números ou caracteres especiais.", 400);
            }


            $chars = '\sª°º\'",-._@#$%()';
            $titleRegex = "/([$chars])\\1+/";

            if (preg_match($titleRegex, $title)) {
                throw new Exception("<strong>Título da notícia</strong> contém sequência não permitida de caracteres.", 400);
            }


            $firstChar = substr($title, 0, 1);

            if (trim(preg_replace("/$firstChar/", "", $title)) == "") {
                throw new Exception("<strong>Título da notícia</strong> inválido. Os caracteres não devem ser todos iguais.", 400);
            }


            if (
                preg_match("/^\s*\d+(\s*\d+)*\s*$/", $title) ||
                preg_match("/^\s*[\W_]+(\s*[\W_]+)*\s*$/", $title) ||
                preg_match("/(.)\\1{7,}/", $title)
            ) {
                throw new Exception("<strong>Título da notícia</strong> inválido.", 400);
            }


            if (strlen($title) < 10 || strlen($title) > 100) {
                throw new Exception("<strong>Título da notícia</strong> deve ter entre 10 e 100 catacteres.", 400);
            }

            $this->title = $title;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }




    /**
     * Método responsável por retornar o resumo da notícia
     *
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * Método responsável por atribuir um resumo à notícia
     * @param string $summary
     * @return void
     */
    public function setSummary(string $summary): void
    {
        try {

            if (trim($summary) == "") {
                throw new Exception("<strong>Resumo da notícia</strong> é obrigatório.", 400);
            }

            $summaryRegex = "/[^a-zA-ZàáâãéêíóôõúÀÁÂÃÉÊÍÓÔÕÚçÇ0-9\sª°º'\",-._@#$%()]/";

            if (preg_match($summaryRegex, $summary)) {
                throw new Exception("<strong>Resumo da notícia</strong> contém caracteres inválidos.", 400);
            }

            $summaryRegex = "/^[^a-zA-ZàáâãéêíóôõúÀÁÂÃÉÊÍÓÔÕÚçÇ]/";
            if (preg_match($summaryRegex, $summary)) {
                throw new Exception("<strong>Resumo da notícia</strong> não deve iniciar com números ou caracteres especiais.", 400);
            }


            $chars = '\sª°º\'",-._@#$%()';
            $summaryRegex = "/([$chars])\\1+/";

            if (preg_match($summaryRegex, $summary)) {
                throw new Exception("<strong>Resumo da notícia</strong> contém sequência não permitida de caracteres.", 400);
            }


            if (strlen($summary) < 100 || strlen($summary) > 150) {
                throw new Exception("<strong>Resumo da notícia</strong> deve ter entre 100 e 150 caracteres.", 400);
            }

            $this->summary = $summary;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }




    /**
     * Método responsável por retornar o corpo da notícia
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Método responsável por atribuir um corpo à notícia
     * @param string $body
     * @return void
     */
    public function setBody(string $body): void
    {
        try {
            if (trim($body) == "") {
                throw new Exception("<strong>Corpo da notícia</strong> é obrigatório.", 400);
            }

            $this->body = $body;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }




    /**
     * Método responsável por retornar o caminho da imagem da notícia
     *
     * @return string
     */
    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    /**
     * Método responsável por atribuir um caminho de imagem da notícia
     * @param string $imagePath
     * @return void
     */
    public function setImagePath(string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }



    /**
     * Método responsável por retornar o autor da notícia
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Método responsável por atribuir um autor à notícia
     * @param string $author
     * @return void
     */
    public function setAuthor(string $author): void
    {
        try {
            $authorRegex = "/^(?=.{5,20}$)(?![_.])(?!.*[_.]{2})[a-z0-9._]+(?<![_.])$/";

            if (!preg_match($authorRegex, $author)) {
                throw new Exception("Não foi possível definir o autor da notícia.", 400);
            }

            $this->author = $author;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por retornar a data de agendamento da notícia
     * @return string
     */
    public function getSchedulingDate(): string
    {
        return $this->schedulingDate;
    }

    /**
     * Método responsável por atribuir uma data de agendamento à notícia
     * @param string $schedulingDate
     * @return void
     */
    public function setSchedulingDate(string $schedulingDate): void
    {
        try {

            $schedulingDateRegex = "/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/";

            if (
                !preg_match($schedulingDateRegex, $schedulingDate) ||
                (new DateTime($schedulingDate))->format("Y-m-d H:i:s") != $schedulingDate
            ) {
                throw new Exception("<strong>Data de agendamento</strong> inválida.", 400);
            }

            $now = new DateTime();
            $schedulingDate = new DateTime($schedulingDate);


            if ($schedulingDate->format("Y-m-d H:i") !== $now->format("Y-m-d H:i") && $schedulingDate < $now) {
                throw new Exception("A <strong>data de agendamento</strong> não pode ser anterior à data atual.", 400);
            }

            $this->schedulingDate = $schedulingDate->format("Y-m-d H:i:s");
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




    /**
     * Método responsável por retornar o status de destaque da notícia (yes ou no)
     *
     * @return string
     */
    public function getHighlighted(): string
    {
        return $this->highlighted;
    }

    /**
     * Método responsável por atribuir um status de destaque à notícia
     * @param string $highlighted
     * @return void
     */
    public function setHighlighted(string $highlighted): void
    {
        try {

            if (!in_array($highlighted, ["yes", "no"])) {
                throw new Exception("Status de destaque da notícia inválido.");
            }

            $this->highlighted = $highlighted;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }





    /**
     * Método responsável por retornar o status de visibilidade da notícia (yes ou no)
     *
     * @return string
     */
    public function getVisible(): string
    {
        return $this->visible;
    }

    /**
     * Método responsável por atribuir um status de visibilidade à notícia
     * @param string $visible
     * @return void
     */
    public function setVisible(string $visible): void
    {
        try {

            if (!in_array($visible, ["yes", "no"])) {
                throw new Exception("Status de visibilidade da notícia inválido.");
            }

            $this->visible = $visible;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
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
     * Método responsável por validar a imagem da notícia
     * @param string $tmpName
     * @return void
     */
    public function validateImage(string $tmpName)
    {

        $validTypes = ["image/jpeg", "image/png", "image/webp", "image/svg+xml"];

        try {

            if (!file_exists($tmpName)) {
                throw new Exception("Problema ao carregar </strong>imagem da notícia<strong>.", 400);
            }

            if (filesize($tmpName) > 5000000) {
                throw new Exception("Tamanho máximo de 5MB excedido para <strong>imagem da notícia</strong>.", 400);
            }

            if (!in_array(mime_content_type($tmpName), $validTypes)) {
                throw new Exception("Tipo de arquivo inválido para <strong>imagem da notícia</strong>.", 400);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por salvar a imagem da notícia, retornando o nome do arquivo
     * @param string $tmpName
     * @param string $currentName
     * @return string
     */
    public function saveImage(string $tmpName, string $currentName): string
    {

        $path = __DIR__ . "/../../resources/images/news";

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

            if (!move_uploaded_file($tmpName, "$path/$newName")) {
                throw new Exception("Não foi possível salvar <strong>imagem da notícia</strong>.", 400);
            };

            return $newName;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por validar imagem carregada no editor de notícia
     * @param string $tmpName
     * @return void
     */
    public function validateEditorUploaderImage(string $tmpName)
    {

        $validTypes = ["image/jpeg", "image/png", "image/webp", "image/svg+xml"];

        try {

            if (!file_exists($tmpName)) {
                throw new Exception("Não foi possível carregar o arquivo de imagem.", 400);
            }

            if (filesize($tmpName) > 5000000) {
                throw new Exception("Tamanho máximo de 5MB excedido para arquivo de imagem.", 400);
            }

            if (!in_array(mime_content_type($tmpName), $validTypes)) {
                throw new Exception("Tipo de arquivo inválido para a imagem.", 400);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por salvar imagem carregada no editor de notícia
     * @param string $tmpName
     * @return string
     */
    public function saveEditorUploaderImage(string $tmpName): string
    {

        $path = __DIR__ . "/../../resources/images/editor";

        try {

            //GERA UM NOME RANDÔMICO PARA A IMAGEM
            $newName = $this->generateRandomString(64);


            //SETA A EXTENSÃO DA IMAGEM DE ACORDO COM O MIME TYPE
            $validTypes = ["image/jpeg" => "jpeg", "image/png" => "png", "image/webp" => "webp", "image/svg+xml" => "svg"];
            $mimeType = mime_content_type($tmpName);
            $extension = $validTypes[$mimeType];

            //CONCATENA O NOME ALEATÓRIO À EXTENSÃO
            $newName .= ".$extension";

            if (!move_uploaded_file($tmpName, "$path/$newName")) {
                throw new Exception("Não foi possível carregar o arquivo de imagem", 400);
            };

            return $newName;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por validar o valor da data de agendamento em relação ao valor da opção de agendar
     * @param string $schedulingDateCheckbox
     * @param string $schedulingDate
     * @return void
     */
    public function validateSchedulingDate(string $schedulingDateCheckbox, string $schedulingDate)
    {

        try {

            switch ($schedulingDateCheckbox) {
                case "yes":
                    if ($schedulingDate == "") {
                        throw new Exception("Por favor, informe a <strong>data de agendamento</strong> corretamente.", 400);
                    }
                    break;

                case "no":
                    if ($schedulingDate != "") {
                        throw new Exception(
                            "O valor da <strong>opção de agendar</strong> é incompatível com o valor da <strong>data de agendamento</strong>.",
                            400
                        );
                    }
                    break;

                default:
                    throw new Exception("Valor da <strong>opção de agendar</strong> inválido.", 400);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

}
