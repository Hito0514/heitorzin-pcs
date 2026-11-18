<div class="container">
    <div class="auth-wrap">
        <div class="caixa-form">
            <h1>Criar conta</h1>
            <form method="post" action="<?= url('registro') ?>" data-validate>
                <?= csrf_field() ?>
                <div class="campo">
                    <label for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" required maxlength="100" data-rotulo="O nome" value="<?= e(old('nome')) ?>" autofocus>
                </div>
                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required maxlength="120" data-rotulo="O e-mail" value="<?= e(old('email')) ?>">
                </div>
                <div class="campo">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" required data-rotulo="O telefone" placeholder="(38) 99999-9999" value="<?= e(old('telefone')) ?>">
                </div>
                <div class="campo">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required data-min="6" data-rotulo="A senha">
                    <div class="ajuda">Mínimo de 6 caracteres.</div>
                </div>
                <div class="campo">
                    <label for="senha2">Confirmar senha</label>
                    <input type="password" id="senha2" name="senha2" required data-igual="senha" data-rotulo="A confirmação">
                </div>
                <button class="btn btn-bloco" type="submit">Cadastrar</button>
            </form>
            <p class="center muted" style="margin-bottom:0">Já tem conta? <a href="<?= url('login') ?>"><strong>Entrar</strong></a></p>
        </div>
    </div>
</div>
