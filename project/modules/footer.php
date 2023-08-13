    <div class="footer">
<?php if(getCurrentURL() != normalizeURL(makeURL('index')) && getCurrentURL() != '/') { ?>
        <a href="<?php echo makeURL('index'); ?>">На главную страницу</a>
<?php } ?>
    </div>
</body>
</html>