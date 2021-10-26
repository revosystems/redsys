<?php


namespace Revosystems\RedsysPayment\Services;

class RedsysError
{
    public static function getMessageFromError($error)
    {
        switch ($error) {
            case "msg1":
                return "Ha de rellenar los datos de la tarjeta";
            case "msg2":
                return "La tarjeta es obligatoria";
            case "msg3":
                return "La tarjeta ha de ser numérica";
            case "msg4":
                return"La tarjeta no puede ser negativa";
            case "msg5":
                return"El mes de caducidad de la tarjeta es obligatorio";
            case "msg6":
                return"El mes de caducidad de la tarjeta ha de ser numérico";
            case "msg7":
                return"El mes de caducidad de la tarjeta es incorrecto";
            case "msg8":
                return"El año de caducidad de la tarjeta es obligatorio";
            case "msg9":
                return"El año de caducidad de la tarjeta ha de ser numérico";
            case "msg10":
                return"El año de caducidad de la tarjeta no puede ser negativo";
            case "msg11":
                return"El código de seguridad de la tarjeta no tiene la longitud correcta";
            case "msg12":
                return"El código de seguridad de la tarjeta ha de ser numérico";
            case "msg13":
                return"El código de seguridad de la tarjeta no puede ser negativo";
            case "msg14":
                return"El código de seguridad no es necesario para su tarjeta";
            case "msg15":
                return"La longitud de la tarjeta no es correcta";
            case "msg16":
                return"Debe Introducir un número de tarjeta válido (sin espacios ni guiones).";
            case "msg17":
                return"Validación incorrecta por parte del comercio";
            default:
                return "Redsys error";
        }
    }
}
