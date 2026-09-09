<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Lançada dentro da transação de agendamento quando o horário escolhido já
 * está ocupado. O AgendamentoController captura e devolve HTTP 409.
 */
class HorarioIndisponivelException extends RuntimeException
{
}
