<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

initApp();

$controller = new AuthController();
$controller->clienteRegister();
?>

<?php
$title = 'Cadastro Cliente';
include __DIR__ . '/../../views/layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card shadow">
      <div class="card-body">
        <div class="text-center mb-4">
          <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
          <h4 class="mt-2">Cadastro do Cliente</h4>
          <p class="text-muted">Crie sua conta para agendar serviços</p>
        </div>
        <form method="POST">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nome" class="form-label">Nome Completo</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="nome" name="nome"
                  value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>"
                  required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="telefone" class="form-label">Telefone</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="tel" class="form-control" id="telefone" name="telefone"
                  value="<?= isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : '' ?>"
                  placeholder="(11) 99999-9999" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" class="form-control" id="email" name="email"
                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="senha" class="form-label">Senha</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="senha" name="senha"
                  minlength="6" required>
              </div>
              <small class="form-text text-muted">Mínimo 6 caracteres</small>
            </div>
            <div class="col-md-6 mb-3">
              <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha"
                  minlength="6" required>
              </div>
            </div>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-person-plus"></i> Cadastrar
            </button>
          </div>
        </form>
        <hr>
        <div class="text-center">
          <p class="mb-0">Já tem uma conta?</p>
          <a href="/auth/cliente_login.php" class="btn btn-outline-primary">
            <i class="bi bi-box-arrow-in-right"></i> Entrar
          </a>
        </div>
        <div class="text-center mt-3">
          <small class="text-muted">
            É barbeiro? <a href="/auth/barbeiro_login.php">Acesse aqui</a>
          </small>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>