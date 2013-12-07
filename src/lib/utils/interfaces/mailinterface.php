<?php
interface MailInterface{
    public function getParameters(array $parameters);
    public function getTemplate();
    public function setMailBody(array $array);
    public function sendMail();
}