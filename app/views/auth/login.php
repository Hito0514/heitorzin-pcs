<div class="container">
    <div class="auth-wrap">
        <div class="caixa-form">
            <h1>Entrar</h1>
            <form method="post" action="<?= url('login') ?>" data-validate>
                <?= csrf_field() ?>
                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required data-rotulo="O e-mail" value="<?= e(old('email')) ?>" autofocus>
                </div>
                <div class="campo">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required data-rotulo="A senha">
                </div>
                <button class="btn btn-bloco" type="submit">Entrar</button>
            </form>
            <p class="center muted" style="margin-bottom:0">Ainda não tem conta? <a href="<?= url('registro') ?>"><strong>Cadastre-se</strong></a></p>

            <div class="teste-box">
                <strong>Contas de teste:</strong><br>
                Admin: <code>admin@heitorzinpcs.com</code> / <code>admin123</code><br>
                Cliente: <code>cliente@teste.com</code> / <code>cliente123</code>
            </div>
        </div>
    </div>
</div>
