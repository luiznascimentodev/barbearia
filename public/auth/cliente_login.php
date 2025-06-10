<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

initApp();

$controller = new AuthController();
$controller->clienteLogin();
?>

<?php
$title = 'Login Cliente';
include __DIR__ . '/../../views/layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-4">
    <div class="card shadow">
      <div class="card-body">
        <div class="text-center mb-4">
          <i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i>
          <h4 class="mt-2">Login do Cliente</h4>
          <p class="text-muted">Entre na sua conta para agendar serviços</p>
        </div>

        <form method="POST">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" class="form-control" id="email" name="email"
                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                required>
            </div>
          </div>

          <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>
          </div>
        </form>

        <hr>

        <div class="text-center">
          <p class="mb-0">Não tem uma conta?</p>
          <a href="/auth/cliente_register.php" class="btn btn-outline-primary">
            <i class="bi bi-person-plus"></i> Cadastrar-se
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