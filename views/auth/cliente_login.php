<?php
$title = 'Login Cliente';
include __DIR__ . '/../layouts/header.php';
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
          <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <div class="mt-3 text-center">
          É barbeiro? <a href="<?= BASE_URL ?>auth/barbeiro_login.php">Acesse aqui</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>