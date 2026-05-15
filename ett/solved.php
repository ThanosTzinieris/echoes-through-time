<div class="solved-overlay <?php if ($solved) echo 'pending'; ?>">

    <!-- RIGHT: Text + Button (FIRST now) -->
    <div class="solved-right">

        <?php $has_next = file_exists(__DIR__ . "/" . $next_level); ?>

        <div class="solved-text">
            <p>
            <?php echo $solved_text; ?>

            <?php if (!$has_next): ?>
                <br><br>
                Time to rest, weary traveler. You have earned it.<br>
                The echoes will return soon.
            <?php endif; ?>
            </p>
        </div>

    <?php if ($has_next): ?>
        <form action="<?php echo $next_level; ?>" method="GET">
            <button type="submit" class="submit-button">
                <img src="../images/submit.png" alt="Next">
            </button>
        </form>
    <?php endif; ?>

    </div>

    <!-- LEFT: Sage (SECOND now) -->
    <div class="solved-left">
        <img src="../images/sage_solved.png" alt="Sage" class="guide-image">
    </div>

</div>