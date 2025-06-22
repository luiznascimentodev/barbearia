<?php
if (!function_exists('getStatusBadge')) {
  function getStatusBadge($status)
  {
    switch ($status) {
      case 'confirmado':
        return '<span class="status-badge badge-confirmado"><i class="bi bi-check-circle"></i> Confirmado</span>';
      case 'pendente':
        return '<span class="status-badge badge-pendente"><i class="bi bi-clock"></i> Pendente</span>';
      case 'cancelado':
        return '<span class="status-badge badge-cancelado"><i class="bi bi-x-circle"></i> Cancelado</span>';
      case 'concluido':
        return '<span class="status-badge badge-concluido"><i class="bi bi-trophy"></i> Concluído</span>';
      default:
        return '<span class="status-badge badge-secondary">' . ucfirst($status) . '</span>';
    }
  }
}

/**
 * Partial para exibir ações de agendamento
 */
if (!function_exists('getAgendamentoActions')) {
  function getAgendamentoActions($agendamento, $userType)
  {
    $actions = [];
    $agendamentoId = $agendamento['id'];
    $status = $agendamento['status'];
    $dataAgendamento = new DateTime($agendamento['data_hora']);
    $agora = new DateTime();

    // Não permitir nenhuma ação se finalizado ou cancelado
    if (in_array($status, ['finalizado', 'cancelado'])) {
      return '';
    }

    // Para barbeiros
    if ($userType === 'barbeiro') {
      if ($status === 'pendente') {
        $actions[] = '<a href="confirmar_agendamento.php?id=' . $agendamentoId . '" class="btn btn-success btn-sm"><i class="bi bi-check"></i> Confirmar</a>';
      }
      if ($status === 'confirmado') {
        $actions[] = '<a href="finalizar_agendamento.php?id=' . $agendamentoId . '" class="btn btn-outline-primary btn-sm"><i class="bi bi-check-circle"></i> Finalizar</a>';
        $actions[] = '<a href="cancelar_agendamento.php?id=' . $agendamentoId . '" class="btn btn-danger btn-sm"><i class="bi bi-x"></i> Cancelar</a>';
      }
    }

    // Para clientes
    if ($userType === 'cliente') {
      $agora->add(new DateInterval('PT2H'));
      if ($status !== 'cancelado' && $dataAgendamento > $agora) {
        $actions[] = '<a href="cancelar_agendamento.php?id=' . $agendamentoId . '" class="btn btn-outline-danger btn-sm"><i class="bi bi-x"></i> Cancelar</a>';
      }
    }
    return implode(' ', $actions);
  }
}

if (!function_exists('renderStatsCard')) {
  /**
   * Partial para card de estatísticas
   */
  function renderStatsCard($title, $value, $icon, $color = 'primary', $description = null)
  {
    echo '<div class="col-md-3 mb-3">';
    echo '    <div class="card text-center card-hover h-100">';
    echo '        <div class="card-body">';
    echo '            <div class="text-' . $color . ' mb-2">';
    echo '                <i class="bi bi-' . $icon . ' fs-1"></i>';
    echo '            </div>';
    echo '            <h5 class="card-title">' . $value . '</h5>';
    echo '            <p class="card-text text-muted">' . $title . '</p>';
    if ($description) {
      echo '            <small class="text-muted">' . $description . '</small>';
    }
    echo '        </div>';
    echo '    </div>';
    echo '</div>';
  }
}

/**
 * Partial para botão de voltar
 */
if (!function_exists('renderBackButton')) {
  function renderBackButton($url, $text = 'Voltar')
  {
    if (strpos($url, 'http') !== 0 && strpos($url, BASE_URL) !== 0) {
      $url = BASE_URL . ltrim($url, '/');
    }
    echo '<a href="' . $url . '" class="btn btn-outline-secondary mb-3">';
    echo '    <i class="bi bi-arrow-left"></i> ' . $text;
    echo '</a>';
  }
}

/**
 * Partial para empty state (quando não há dados)
 */
if (!function_exists('renderEmptyState')) {
  function renderEmptyState($message, $icon = 'inbox', $action = null)
  {
    echo '<div class="text-center py-5">';
    echo '    <div class="text-muted mb-3">';
    echo '        <i class="bi bi-' . $icon . '" style="font-size: 4rem;"></i>';
    echo '    </div>';
    echo '    <h5 class="text-muted">' . $message . '</h5>';
    if ($action) {
      echo '    <div class="mt-3">' . $action . '</div>';
    }
    echo '</div>';
  }
}

/**
 * Estado vazio (empty state) para listas e dashboards
 * Compatível com uso: getEmptyState(mensagem, [descricao], [icone], [acao])
 */
if (!function_exists('getEmptyState')) {
  function getEmptyState($message, $description = '', $icon = 'inbox', $action = null)
  {
    echo '<div class="text-center py-5">';
    echo '    <div class="text-muted mb-3">';
    echo '        <i class="bi bi-' . $icon . '" style="font-size: 4rem;"></i>';
    echo '    </div>';
    echo '    <h5 class="text-muted">' . $message . '</h5>';
    if ($description) {
      echo '    <p class="text-muted">' . $description . '</p>';
    }
    if ($action) {
      echo '    <div class="mt-3">' . $action . '</div>';
    }
    echo '</div>';
  }
}

/**
 * Formata a data para o padrão d/m/Y
 */
if (!function_exists('formatDate')) {
  function formatDate($date)
  {
    if (!$date) return '';
    return date('d/m/Y', strtotime($date));
  }
}
