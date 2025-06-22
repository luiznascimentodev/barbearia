<?php
$title = 'Cadastro Cliente';
include __DIR__ . '/../layouts/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card shadow">
      <div class="card-body">
        <div class="text-center mb-4">
          <i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i>
          <h4 class="mt-2">Cadastro do Cliente</h4>
          <p class="text-muted">Crie sua conta para agendar serviços</p>
        </div>
        <form method="POST">
          <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control" id="nome" name="nome"
                value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>"
                required>
            </div>
          </div>
          <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-telephone"></i></span>
              <input type="text" class="form-control" id="telefone" name="telefone"
                value="<?= isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : '' ?>"
                required>
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
          <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
        </form>
        <div class="mt-3 text-center">
          Já possui conta? <a href="<?= BASE_URL ?>auth/cliente_login.php">Entrar</a><br>
          É barbeiro? <a href="<?= BASE_URL ?>auth/barbeiro_register.php">Cadastre-se aqui</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>