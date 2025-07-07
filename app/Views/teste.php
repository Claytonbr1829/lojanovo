<div>
    <h3><?= $titulo ?></h3>
    <table border ="1">
        <thead>
            <tr>
                <th width = "25%">ID</th>
                <th width = "25%">email</th>
                <th width = "25%">Senha</th>
                <th width = "25%">ID Empresa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($clientes as $cliente): ?>
                <tr>
                    <td><?= $cliente->id_cliente ?></td>
                    <td><?= $cliente->email ?></td>
                    <td><?= $cliente->senha ?></td>
                    <td><?= $cliente->id_empresa ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>