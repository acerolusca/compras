<?php


namespace App\Communication;

require_once __DIR__ . "/../../includes/app.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;



class Email {

     /**
      * Mensagem de erro de envio
      * @var string
      */

      private $error;



    /**
     * Método responsável por retornar a mensagem de erro do envio
     *
     * @return string
     */
      public function getError(){
        return $this->error;
      }


/**
 * Método responsável por enviar um email
 *
 * @param string|array $addresses
 * @param string $subject
 * @param string $body
 * @param string|array $attachments
 * @param string|array $ccs
 * @param string|array $bccs
 * @return boolean
 */
      public function sendEmail($addresses, $subject, $body, $attachments = [], $ccs = [], $bccs = []){

        //LIMPAR A MENSAGEM DE ERRO
        $this->error = "";

        //INSTÂNCIA DE PHP MAILER
        $obMail = new PHPMailer(true);

        try{

            //CREDENCIAIS DE ACESSO AO SMTP
            $obMail->isSMTP();
            $obMail->Host = getenv("SMTP_HOST");
            $obMail->SMTPAuth = true;
            $obMail->Username = getenv("SMTP_USER");
            $obMail->Password = getenv("SMTP_PASS");
            $obMail->SMTPSecure = getenv("SMTP_SECURE");
            $obMail->Port = getenv("SMTP_PORT");
            $obMail->CharSet = getenv("SMTP_CHARSET");



            //REMETENTE
            $obMail->setFrom(getenv("SMTP_FROM_EMAIL"), getenv("SMTP_FROM_NAME"));


            //DESTINATÁRIOS
            $addresses = is_array($addresses) ? $addresses : [$addresses];

            foreach ($addresses as $address){
                $obMail->addAddress($address);
            }



            //ANEXOS
            $attachments = is_array($attachments) ? $attachments : [$attachments];

            foreach ($attachments as $attachment){
                $obMail->addAttachment($attachment);
            }
            


            //CC
            $ccs = is_array($ccs) ? $ccs : [$ccs];

            foreach ($ccs as $cc){
                $obMail->addCC($cc);
            }



            //BCC
            $bccs = is_array($bccs) ? $bccs : [$bccs];

            foreach ($bccs as $bcc){
                $obMail->addBCC($bcc);
            }




            //CONTEÚDO DO E-MAIL
            $obMail->isHTML(true);
            $obMail->Subject = $subject;
            $obMail->Body = $body;


            return $obMail->send();
            

        }catch( PHPMailerException $e){
            $this->error = $e->getMessage();
            return false;
        }


      }

}