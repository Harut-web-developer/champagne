<?php
$this->params['sub_page'] = $sub_page;
?>
<div class="container">
    <div class="row justify-content-center align-items-center">
        <ul class="sortable-ul list-group">
            <?php foreach ($result as $row): ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($row['name']); ?>
                    <input type="hidden" name="sort[]" value="<?php echo $row['id']; ?>">
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>
    $('.sortable-ul').sortable({
        stop: function() {
            $.ajax({
                url: '/route/save',
                method: 'post',
                data: $('.sortable-ul input').serialize(),
                success: function(response) {
                },
            });
        }
    });
</script>
