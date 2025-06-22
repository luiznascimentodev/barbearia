<?php
$title = 'Meu Perfil';
$breadcrumb = [
  ['title' => 'Dashboard', 'url' => BASE_URL . 'cliente/dashboard.php'],
  ['title' => 'Meu Perfil']
];
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../partials/components.php';
?>
<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>cliente/dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Meu Perfil</li>
        </ol>
      </nav>
      <?php include __DIR__ . '/../partials/messages.php'; ?>
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i> Meu Perfil</h5>
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($cliente['nome']) ?>" required>
              </div>
              <div class="col-md-6">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="tel" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">E-mail</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($cliente['email']) ?>" required>
            </div>
            <hr>
            <h6 class="text-muted mb-3">Alterar Senha</h6>
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="senha" class="form-label">Nova Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" minlength="6">
              </div>
              <div class="col-md-6">
                <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6">
              </div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar Alterações</button>
              <a href="<?= BASE_URL ?>cliente/dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>