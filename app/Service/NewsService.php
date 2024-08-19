<?php

namespace App\Service;

use App\Model\News;
use App\Model\User;
use App\Repository\NewsRepository;
use App\Utils\View;


use \App\Session\Session;
use \Exception;
use \DateTime;
use \PDO;


class NewsService
{


    /**
     * Instância de NewsRepository
     *
     * @var NewsRepository
     */
    private NewsRepository $newsRepository;




    /**
     * Construtor da classe
     * @param NewsRepository $newsRepository
     */
    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }


    /**
     * Método responsável pelo cadastro de uma notícia
     *
     * @param array $newsRegisterData
     * @return void
     */
    public function register(array $newsRegisterData): void
    {
        try {

            $news = new News();

            //TÍTULO, RESUMO E CORPO
            $news->setTitle($newsRegisterData["registerTitle"] ?? "");
            $news->setSummary($newsRegisterData["registerSummary"] ?? "");
            $news->setBody($newsRegisterData["registerBody"] ?? "");

            // var_dump($news->getBody());
            // exit();

            //IMAGEM
            $imageTmpName = $newsRegisterData["imageTmpName"];
            if (!empty($imageTmpName)) {
                $news->validateImage($imageTmpName);
                $newImageName = $news->saveImage($imageTmpName, getenv("DEFAULT_NEWS_IMAGE_PATH"));
                $news->setImagePath($newImageName);
            } else {
                $news->setImagePath(getenv("DEFAULT_NEWS_IMAGE_PATH"));
            }


            //AUTOR
            $user = new User();
            $user->setLoggedInfo();
            $news->setAuthor($user->getUsername());


            //DATA DE REGISTRO
            $currentDate = new DateTime();
            $news->setRegisterDate($currentDate->format("Y-m-d H:i:s"));


            //DATA DE AGENDAMENTO
            $schedulingDateCheckbox = $newsRegisterData["registerSchedulingDateCheckbox"] ?? "";
            $schedulingDate = $newsRegisterData["registerSchedulingDate"] ?? "";

            $news->validateSchedulingDate($schedulingDateCheckbox, $schedulingDate);
            $schedulingDate = empty($schedulingDate) ? $currentDate->format("Y-m-d H:i:s") : (new DateTime($schedulingDate))->format("Y-m-d H:i:s");
            $news->setSchedulingDate($schedulingDate);


            //DESTAQUE
            $news->setHighlighted($newsRegisterData["registerHighlighted"] ?? "");


            //VISIBILIDADE
            $news->setVisible($newsRegisterData["registerVisible"] ?? "");

            $this->newsRepository->insert($news);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }




    /**
     * Método responsável por retornar as informações de todas as notícias cadastradas no banco
     *
     * @return array
     */
    public function getAll(): array
    {

        try {

            $statement = $this->newsRepository->getAll();

            $newsList = [];

            while ($news = $statement->fetchObject(News::class)) {

                $schedulingDate = (new DateTime($news->getSchedulingDate()))->format("d/m/Y H:i:s") ?? "";
                $highlighted = ($news->getHighlighted() ?? "no") == "yes";
                $visible = ($news->getVisible() ?? "no") == "yes";

                $news = [
                    "id" => $news->getId() ?? "",
                    "title" => $news->getTitle() ?? "",
                    "imagePath" =>  "/image/news/" . ($news->getImagePath() ?? getenv("DEFAULT_NEWS_IMAGE_PATH")),
                    "author" => $news->getAuthor() ?? "",
                    "schedulingDate" =>  explode(" ", $schedulingDate)[0] ?? "",
                    "schedulingTime" => explode(" ", $schedulingDate)[1] ?? "",
                    "highlighted" => $highlighted ? true : false,
                    "highlightedButtonClass" => "fa-" . ($highlighted ? "solid" : "regular"),
                    "highlightedButtonTitle" => $highlighted ? "Remover dos destaques" : "Adicionar aos destaques",
                    "visible" => $visible ? true : false,
                    "visibleButtonClass" => "fa-" . ($visible ? "eye" : "eye-slash"),
                    "visibleButtonTitle" => $visible ? "Ocultar" : "Tornar visível",
                ];

                array_push($newsList, $news);
            }

            return $newsList;
        } catch (Exception $e) {
            return [];
        }
    }



    /**
     * Método responsável por retornar um array com as informações de uma notícia buscada a partir do seu ID
     *
     * @param string $newsId
     * @return array
     */
    public function getInfo(string $newsId): array
    {
        try {

            if(!is_numeric($newsId)){
                throw new Exception("Parâmetro de notícia inválido.", 400);
            }

            $statement = $this->newsRepository->getById($newsId);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "É possível que ela tenha sido excluído por outro usuário enquanto você tentava editá-la.",
                    404
                );
            }

            $news = $statement->fetchObject(News::class);


            $registerDate = new DateTime($news->getRegisterDate()) ?? "";
            $schedulingDate = new DateTime($news->getSchedulingDate()) ?? "";

            if (empty($registerDate) || empty($schedulingDate)) {
                throw new Exception("Não foi possível carregar os dados da notícia.");
            }

            $registerEqualScheduling = $registerDate->format("Y-m-d H:i:s") === $schedulingDate->format("Y-m-d H:i:s");
            $schedulingLessThanNow = $schedulingDate < (new DateTime());
            $schedulingNotAllowed = $registerEqualScheduling || $schedulingLessThanNow;

            $newsInfo = [];

            $newsInfo["id"] = $news->getId() ?? "";
            $newsInfo["title"] = $news->getTitle() ?? "";
            $newsInfo["summary"] = $news->getSummary() ?? "";
            $newsInfo["body"] = $news->getBody() ?? "";
            $newsInfo["imagePath"] = "/image/news/" . ($news->getImagePath() ?? getenv("DEFAULT_NEWS_IMAGE_PATH"));
            $newsInfo["author"] = $news->getAuthor() ?? "";
            $newsInfo["registerDate"] = $news->getRegisterDate() ?? "";
            $newsInfo["schedulingDate"] = $schedulingNotAllowed ? "" : $schedulingDate->format("Y-m-d\TH:i");
            $newsInfo["schedulingDateCheckbox"] = $schedulingNotAllowed ? false : true;
            $newsInfo["schedulingDateContainer"] = $schedulingNotAllowed ? "none" : "flex";
            $newsInfo["highlighted"] = ($news->getHighlighted() ?? "no") == "yes" ? true : false;
            $newsInfo["visible"] = ($news->getVisible() ?? "no") == "yes" ? true : false;

            return $newsInfo;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }


    /**
     * Método responsável pela edição das informações de uma notícia
     * @param array $newsEditData
     * @return boolean
     */
    public function edit(array $newsEditData): bool
    {
        try {
            //GARANTE QUE A CHAVE "editNewsId" ESTEJA DEFINIDA EM $newsEditData
            $newsEditData["editNewsId"] ??= "";

            $statement = $this->newsRepository->getById($newsEditData["editNewsId"]);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "É possível que ela tenha sido excluída por outro usuário enquanto você tentava editá-la.",
                    404
                );
            }

            unset($newsEditData["editNewsId"]);


            $newsFound = $statement->fetchObject(News::class);
            $newsId = $newsFound->getId();


            //IMAGEM
            $imageTmpName = $newsEditData["imageTmpName"] ?? "";
            $imageCurrentName = $newsFound->getImagePath();
            if (!empty($imageTmpName)) {
                $newsFound->validateImage($imageTmpName);
                $newImageName = $newsFound->saveImage($imageTmpName, $imageCurrentName);
                $newsFound->setImagePath($newImageName);
            }


            //DATA DE AGENDAMENTO
            $schedulingDateCheckbox = $newsEditData["editSchedulingDateCheckbox"] ??= "";
            $schedulingDate = $newsEditData["editSchedulingDate"] ??= "";
            $newsFound->validateSchedulingDate($schedulingDateCheckbox, $schedulingDate);

            unset($newsEditData["editSchedulingDateCheckbox"]);

            if (empty($schedulingDate)) {
                unset($newsEditData["editSchedulingDate"]);
            }

            $this->setDataOnNewsFound($newsEditData, $newsFound);

            return $this->newsRepository->update($newsFound, $newsId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por atribuir as alterações enviadas no formulário de edição à notícia encontrada
     * @param array $userEditData
     * @param News $newsFound
     * @return News
     */
    private function setDataOnNewsFound(array $data, News $newsFound): News
    {

        try {

            //LÓGICA PARA VERIFICAR O ENVIO DE IMAGEM
            if (array_key_exists("imageTmpName", $data)) {
                $changesCounter = (!empty($data["imageTmpName"])) ? 1 : 0;
                unset($data["imageTmpName"]);
            } else {
                $changesCounter = 0;
            }


            //EXTRAINDO O PREFIXO DAS CHAVES DO ARRAY DE DADOS
            $data = $this->removePrefixFromArrayKeys("edit", $data);



            //PERCORRE O ARRAY COM OS DADOS RECÉM ENVIADOS NO FORMULÁRIO DE EDIÇÃO
            foreach ($data as $property => $value) {

                //CAPTURA O VALOR ATUAL DE DETERMINADA PROPRIEDADE DA NOTÍCIA
                $currentValue = $this->callUserGetterMethodByProperty($newsFound, $property);

                //SE O VALOR ATUAL FOR DIFERENTE DO CORRESPONDENTE NO ARRAY COM OS DADOS DO FORMULÁRIO DE EDIÇÃO
                if ($currentValue != $value) {

                    //ATRIBUI O VALOR ENVIADO NO FORMULÁRIO À PROPRIEDADE CORRESPONDENTE
                    $this->callUserSetterMethodByProperty($newsFound, $property, $value);
                    $changesCounter++;
                }
            }

            if ($changesCounter == 0) {
                throw new Exception("Nenhuma alteração foi detectada.");
            }

            return $newsFound;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por chamar Getter específico da classe News de acordo com a propriedade
     * @param News $news
     * @param string $property
     * @return mixed
     */
    private function callUserGetterMethodByProperty(News $news, string $property): mixed
    {
        try {
            $getterMethod = "get" . ucfirst($property);
            if (!method_exists($news, $getterMethod)) {
                throw new Exception("Método $getterMethod não foi definido!", 400);
            }
            return $news->$getterMethod();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por chamar Setter específico da classe News de acordo com a propriedade
     * @param News $news
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    private function callUserSetterMethodByProperty(News $news, string $property, mixed $value): mixed
    {
        try {
            $setterMethod = "set" . ucfirst($property);
            if (!method_exists($news, $setterMethod)) {
                throw new Exception("Método $setterMethod não foi definido!", 400);
            }
            return $news->$setterMethod($value);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }


    /**
     * 
     * Método responsável por verificar garantir que não haja duplicatas para campos que devem ser únicos no banco
     * @param string $key
     * @param mixed $value
     * @param string $label
     * @return void
     */
    private function ensureIsUnique(string $key, mixed $value, string $label): void
    {
        try {

            $isUnique = $this->newsRepository->isUnique($key, $value);

            if (!$isUnique) {
                throw new Exception("Valor de <strong>$label</strong> já existe.", 400);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por remover um prefixo das chaves de um array
     * @param array $editArray
     * @return array
     */
    private function removePrefixFromArrayKeys(string $prefix, array $array): array
    {
        $newEditArray = [];
        foreach ($array as $currentKey => $value) {
            $newKey = lcfirst(str_replace($prefix, "", $currentKey));
            $newEditArray[$newKey] = $value;
        }

        return $newEditArray;
    }



    /**
     * Método responsável por deletar uma notícia
     *
     * @param string $newsId
     * @return boolean
     */
    public function delete(string $newsId): bool
    {
        try {

            $statement = $this->newsRepository->getById($newsId);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "É possível que ela tenha sido excluída por outro usuário enquanto você tentava excluí-la.",
                    404
                );
            }


            return $this->newsRepository->delete($newsId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }


    /**
     * Método responsável por alterar o status de destaque de uma notícia
     *
     * @param array $changeHighlightedData
     * @return boolean
     */
    public function changeHighlighted(array $changeHighlightedData): bool
    {
        try {

            //GARANTE QUE A CHAVE "newsId" ESTEJA DEFINIDA EM $changeHighlightedData
            $changeHighlightedData["newsId"] ??= "";

            $statement = $this->newsRepository->getById($changeHighlightedData["newsId"]);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "É possível que ela tenha sido excluída por outro usuário enquanto você tentava mudar o status de destaque.",
                    404
                );
            }

            unset($changeHighlightedData["newsId"]);

            $newsFound = $statement->fetchObject(News::class);
            $newsId = $newsFound->getId();

            $newsFound->setHighlighted($changeHighlightedData["highlighted"] ?? "");

            return $this->newsRepository->update($newsFound, $newsId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável por alterar o status de visibilidade de uma notícia
     *
     * @param array $changeVisibleData
     * @return boolean
     */
    public function changeVisible(array $changeVisibleData): bool
    {
        try {

            //GARANTE QUE A CHAVE "newsId" ESTEJA DEFINIDA EM $changeVisibleData
            $changeVisibleData["newsId"] ??= "";

            $statement = $this->newsRepository->getById($changeVisibleData["newsId"]);

            if ($statement->rowCount() == 0) {
                throw new Exception(
                    "É possível que ela tenha sido excluída por outro usuário enquanto você tentava mudar o status de visibilidade.",
                    404
                );
            }

            unset($changeVisibleData["newsId"]);

            $newsFound = $statement->fetchObject(News::class);
            $newsId = $newsFound->getId();

            $newsFound->setVisible($changeVisibleData["visible"] ?? "");

            return $this->newsRepository->update($newsFound, $newsId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    /**
     * Método responsável pelo upload de imagem no editor de notíca
     * @param string $imageTmpName
     * @return string
     */
    public function uploadEditorImage(string $imageTmpName): string
    {

        try {

            $news = new News();

            $news->validateEditorUploaderImage($imageTmpName);

            return "/image/editor/" . $news->saveEditorUploaderImage($imageTmpName);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }




    /**
     * Método responsável por retornar as informações de todas as notícias disponíveis
     * @return array
     */
    public function getAllAvailable(): array
    {

        try {

            $statement = $this->newsRepository->getAll();

            $newsList = [];

            while ($news = $statement->fetchObject(News::class)) {

                $schedulingDate = new DateTime($news->getSchedulingDate());
                $now = new DateTime();
                $visible = ($news->getVisible() ?? "no") == "yes";

                if ($schedulingDate <= $now && $visible) {
                    $news = [
                        "id" => $news->getId() ?? "",
                        "title" => $news->getTitle() ?? "",
                        "summary" => $news->getSummary() ?? "",
                        "image" =>  getenv("URL_IP") . "/image/news/" . ($news->getImagePath() ?? getenv("DEFAULT_NEWS_IMAGE_PATH")),
                        "date" =>  $schedulingDate->format("d/m/Y H:i:s"),
                        "category" => $news->getHighlighted() == "yes" ?  "Destaques" : "Notícias"
                    ];

                    array_push($newsList, $news);
                }
            }

            return $newsList;
            
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }




    /**
     * Método responsável por retornar as informações de todas as notícias comuns disponíveis 
     *
     * @return array
     */
    public function getRegularAvailable(): array
    {

        try {

            $statement = $this->newsRepository->getAll();

            $newsList = [];

            while ($news = $statement->fetchObject(News::class)) {

                $schedulingDate = new DateTime($news->getSchedulingDate());
                $now = new DateTime();
                $visible = ($news->getVisible() ?? "no") == "yes";
                $regular = ($news->getHighlighted() ?? "no") == "no";

                if ($schedulingDate <= $now && $visible && $regular) {
                    $news = [
                        "id" => $news->getId() ?? "",
                        "title" => $news->getTitle() ?? "",
                        "summary" => $news->getSummary() ?? "",
                        "image" =>  getenv("URL_IP") . "/image/news/" . ($news->getImagePath() ?? getenv("DEFAULT_NEWS_IMAGE_PATH")),
                        "date" =>  $schedulingDate->format("d/m/Y H:i:s"),
                        "category" => "Notícias"
                    ];

                    array_push($newsList, $news);
                }
            }

            return $newsList;
        } catch (Exception $e) {
            return [];
        }
    }




    /**
     * Método responsável por retornar as informações de todas as notícias em destaque disponíveis 
     *
     * @return array
     */
    public function getHighlightedAvailable(): array
    {
        try {

            $statement = $this->newsRepository->getAll();

            $newsList = [];

            while ($news = $statement->fetchObject(News::class)) {

                $schedulingDate = new DateTime($news->getSchedulingDate());
                $now = new DateTime();
                $visible = ($news->getVisible() ?? "no") == "yes";
                $highlighted = ($news->getHighlighted() ?? "no") == "yes";

                if ($schedulingDate <= $now && $visible && $highlighted) {
                    $news = [
                        "id" => $news->getId() ?? "",
                        "title" => $news->getTitle() ?? "",
                        "summary" => $news->getSummary() ?? "",
                        "image" =>  getenv("URL_IP") . "/image/news/" . ($news->getImagePath() ?? getenv("DEFAULT_NEWS_IMAGE_PATH")),
                        "date" =>  $schedulingDate->format("d/m/Y H:i:s"),
                        "category" => "Destaques"
                    ];

                    array_push($newsList, $news);
                }
            }

            return $newsList;
        } catch (Exception $e) {
            return [];
        }
    }




    /**
     * Método responsável por retornar um array com as informações de uma notícia buscada pelo portal
     *
     * @param string $newsId
     * @return array
     */
    public function getInfoAvailable(string $newsId): array
    {
        try {

            if(!is_numeric($newsId) || empty($newsId)){
                throw new Exception("Parâmetro de notícia inválido ou não informado.", 400);
            }

            $statement = $this->newsRepository->getById($newsId);

            if ($statement->rowCount() == 0) {
                throw new Exception("Essa notícia não está disponível.", 404);
            }

            $news = $statement->fetchObject(News::class);

            $schedulingDate = new DateTime($news->getSchedulingDate());
            $now = new DateTime();
            $visible = ($news->getVisible() ?? "no") == "yes";


            if (!($schedulingDate <= $now && $visible)){
                throw new Exception("Essa notícia não está disponível.", 404);
            }
            
            $newsInfo = [];

            $newsInfo["id"] = $news->getId() ?? "";
            $newsInfo["title"] = $news->getTitle() ?? "";
            $newsInfo["summary"] = $news->getSummary() ?? "";
            $newsInfo["body"] = $this->addPrefixToImageSrc($news->getBody() ?? "", getenv("URL_IP"));
            $newsInfo["image"] = getenv("URL_IP") . "/image/news/" . ($news->getImagePath() ?? getenv("DEFAULT_NEWS_IMAGE_PATH"));
            $newsInfo["date"] = $schedulingDate->format("d/m/Y H:i:s") ?? "";

            return $newsInfo;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }




    /**
     * Método responsável por retornar as informações das notícias correspondentes à busca
     * @param string $searched
     * @return array
     */
    public function search(string $searched): array
    {

        try {

            if(empty($searched)){
                throw new Exception("Parâmetro de busca inválido.", 400);
            }

            $searched = urldecode($searched);

            $statement = $this->newsRepository->search($searched);

            $newsList = [];

            while ($news = $statement->fetchObject(News::class)) {

                $schedulingDate = new DateTime($news->getSchedulingDate());
                $now = new DateTime();
                $visible = ($news->getVisible() ?? "no") == "yes";

                if ($schedulingDate <= $now && $visible) {
                    $news = [
                        "id" => $news->getId() ?? "",
                        "title" => $news->getTitle() ?? "",
                        "summary" => $news->getSummary() ?? "",
                        "image" =>   getenv("URL_IP") . "/image/news/" . ($news->getImagePath() ?? getenv("DEFAULT_NEWS_IMAGE_PATH")),
                        "date" =>  $schedulingDate->format("d/m/Y H:i:s"),
                        "category" => "Notícias"
                    ];

                    array_push($newsList, $news);
                }
            }

            return $newsList;
        } catch (Exception $e) {
            return [];
        }
    }



    /**
     * Método responsável por adicionar prefixo (domínio) aos caminhos das imagens do corpo da notícia
     * @param string $body
     * @param string $prefix
     * @return string
     */
    private function addPrefixToImageSrc(string $body, string $prefix): string
    {
        $pattern = '/<img\s+[^>]*?src\s*=\s*["\']([^"\']+)["\']/i';

        $body = preg_replace_callback(
            $pattern,
            function ($matches) use ($prefix) {
                $originalUrl = $matches[1];
                $newUrl = "$prefix$originalUrl";
                return str_replace($originalUrl, $newUrl, $matches[0]);
            },
            $body
        );

        return $body;
    }
}
