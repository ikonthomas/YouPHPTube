<?php
if (!file_exists('../videos/configuration.php')) {
    if (!file_exists('../install/index.php')) {
        die("No Configuration and no Installation");
    }    
    header("Location: install/index.php");
}

require_once '../videos/configuration.php';

require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/functions.php';

if(!empty($_GET['type'])){
    if($_GET['type']=='audio'){
        $_SESSION['type'] = 'audio';
    }else if($_GET['type']=='video'){
        $_SESSION['type'] = 'video';
    }else{
        $_SESSION['type'] = "";
        unset($_SESSION['type']);
    }
}

require_once $global['systemRootPath'] . 'objects/video.php';
$video = Video::getVideo();
if (!empty($video)) {
    $name = empty($video['name'])?substr($video['user'], 0,5)."...":$video['name'];
    $video['creator'] = '<div class="pull-left"><img src="'.User::getPhoto($video['users_id']).'" alt="" class="img img-responsive img-circle" style="max-width: 40px;"/></div><div class="commentDetails" style="margin-left:45px;"><div class="commenterName"><strong>'.$name.'</strong> <small>'.humanTiming(strtotime($video['videoCreation'])).'</small></div></div>';
}

if (empty($_GET['page'])) {
    $_GET['page'] = 1;
} else {
    $_GET['page'] = intval($_GET['page']);
}
$_POST['rowCount'] = 10;
$_POST['current'] = $_GET['page'];
$_POST['sort']['created'] = 'desc';
$videos = Video::getAllVideos();
foreach ($videos as $key => $value) {
    $name = empty($value['name'])?$value['user']:$value['name'];
    $videos[$key]['creator'] = '<div class="pull-left"><img src="'.User::getPhoto($value['users_id']).'" alt="" class="img img-responsive img-circle" style="max-width: 20px;"/></div><div class="commentDetails" style="margin-left:25px;"><div class="commenterName"><strong>'.$name.'</strong> <small>'.humanTiming(strtotime($value['videoCreation'])).'</small></div></div>';
}
$total = Video::getTotalVideos();
$totalPages = ceil($total / $_POST['rowCount']);
require_once $global['systemRootPath'] . 'objects/configuration.php';
$config = new Configuration();
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $config->getWebSiteTitle(); ?>  :: <?php echo $video['title']; ?></title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
    </head>

    <body>
        <?php
        include 'include/navbar.php';
        ?>
        <div class="container-fluid">

            <?php
            if (!empty($video)) {
                if (empty($_GET['search'])) {
                    if(empty($video['type'])){
                        $video['type'] = "video";
                    }
                    ?>
                    <?php
                    require "{$global['systemRootPath']}view/include/{$video['type']}.php";
                    ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-lg-1"></div>
                        <div class="col-xs-12 col-sm-12 col-lg-6 ">
                            <div class="row bgWhite">
                                <div class="col-xs-4 col-sm-4 col-lg-4 nopadding">
                                    <?php
                                        if($video['type']!=="audio"){
                                            $img = "{$global['webSiteRootURL']}videos/{$video['filename']}.jpg";
                                        }else{
                                            $img = "{$global['webSiteRootURL']}view/img/mp3.png";
                                        }
                                    ?>
                                    <img src="<?php echo $img; ?>" alt="<?php echo $video['title']; ?>" class="img-responsive" height="130px" />        
                                </div>
                                <div class="col-xs-8 col-sm-8 col-lg-8">
                                    <h1>
                                        <?php echo $video['title']; ?>
                                    </h1>
                                    <div class="col-xs-12 col-sm-12 col-lg-12"><?php echo $video['creator']; ?></div>
                                    
                                    <div class="col-xs-4 col-sm-2 col-lg-2 text-right"><strong><?php echo __("Category"); ?>:</strong></div>
                                    <div class="col-xs-8 col-sm-10 col-lg-10"><?php echo $video['category']; ?></div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-lg-12"><?php echo $video['views_count']; ?> <?php echo __("Views"); ?></div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-lg-12"><strong><?php echo __("Description"); ?>:</strong></div>
                                    <div class="col-xs-12 col-sm-12 col-lg-12"><?php echo nl2br($video['description']); ?></div>
                                </div>                                
                            </div>
                            <div class="row bgWhite">
                                <div class="input-group">
                                    <textarea class="form-control custom-control" rows="3" style="resize:none" id="comment" maxlength="200" <?php if(!User::canComment()){ echo "disabled"; } ?>><?php if(!User::canComment()){ echo __("You can not comment videos"); } ?></textarea>     
                                    <?php if(User::canComment()){ ?>
                                    <span class="input-group-addon btn btn-success" id="saveCommentBtn" <?php if(!User::canComment()){ echo "disabled='disabled'"; } ?>><span class="glyphicon glyphicon-comment"></span> <?php echo __("Comment"); ?></span>
                                    <?php }else{ ?>
                                    <a class="input-group-addon btn btn-success" href="<?php echo $global['webSiteRootURL']; ?>user"><span class="glyphicon glyphicon-log-in"></span> <?php echo __("You must login to be able to comment on videos"); ?></a>
                                    <?php } ?>
                                </div>
                                <div class="pull-right" id="count_message"></div>
                                <script>
                                    $(document).ready(function () {
                                        var text_max = 200;
                                        $('#count_message').html(text_max + ' <?php echo __("remaining"); ?>');

                                        $('#comment').keyup(function () {
                                            var text_length = $(this).val().length;
                                            var text_remaining = text_max - text_length;

                                            $('#count_message').html(text_remaining + ' <?php echo __("remaining"); ?>');
                                        });
                                    });
                                </script>
                                <h4><?php echo __("Comments"); ?>:</h4>
                                <table id="grid" class="table table-condensed table-hover table-striped nowrapCell">
                                    <thead>
                                        <tr>
                                            <th data-column-id="comment" ><?php echo __("Comment"); ?></th>
                                        </tr>
                                    </thead>
                                </table>

                                <script>
                                    $(document).ready(function () {
                                        var grid = $("#grid").bootgrid({
                                            ajax: true,
                                            url: "<?php echo $global['webSiteRootURL'] . "comments.json/" . $video['id']; ?>",
                                            sorting: false,
                                            templates: {
                                                header: ""
                                            }
                                        });

                                        $('#saveCommentBtn').click(function () {
                                            if($(this).attr('disabled')==='disabled'){
                                                return false;
                                            }
                                            if ($('#comment').val().length > 5) {
                                                modal.showPleaseWait();
                                                $.ajax({
                                                    url: '<?php echo $global['webSiteRootURL']; ?>saveComment',
                                                    method: 'POST',
                                                    data: {'comment': $('#comment').val(), 'video': "<?php echo $video['id']; ?>"},
                                                    success: function (response) {
                                                        if (response.status === "1") {
                                                            swal("<?php echo __("Congratulations"); ?>!", "<?php echo __("Your comment has been saved!"); ?>", "success");
                                                            $('#comment').val('');
                                                            $('#grid').bootgrid('reload');
                                                        } else {
                                                            swal("<?php echo __("Sorry"); ?>!", "<?php echo __("Your comment has NOT been saved!"); ?>", "error");
                                                        }
                                                        modal.hidePleaseWait();
                                                    }
                                                });
                                            } else {
                                                swal("<?php echo __("Sorry"); ?>!", "<?php echo __("Your comment must be bigger then 5 characters!"); ?>", "error");
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            <div class="row bgWhite">
                                <h4><span class="glyphicon glyphicon-share"></span> <?php echo __("Share Video"); ?>:</h4>
                                <div class="highlight">
                                    <form>
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                   value="<?php
                                                   if($video['type']=='video'){
                                                       $code = '<iframe width="640" height="480" style="max-width: 100%;max-height: 100%;" src="' . $global['webSiteRootURL'] . 'videoEmbeded/' . $video['clean_title'] . '" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
                                                   }else{
                                                       $code = '<iframe width="350" height="40" style="max-width: 100%;max-height: 100%;" src="' . $global['webSiteRootURL'] . 'videoEmbeded/' . $video['clean_title'] . '" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
                                                   }
                                                   echo htmlentities($code);
                                                   ?>" placeholder="<?php echo __("Share Video"); ?>" id="copy-input">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="copy-button"
                                                        data-toggle="tooltip" data-placement="button"
                                                        title="<?php echo __("Copy to Clipboard"); ?>">
                                                    <span class="glyphicon glyphicon-copy"></span>    <?php echo __("Copy"); ?>
                                                </button>
                                                <script>
                                                    $(document).ready(function () {
                                                        // Initialize the tooltip.
                                                        $('#copy-button').tooltip();

                                                        // When the copy button is clicked, select the value of the text box, attempt
                                                        // to execute the copy command, and trigger event to update tooltip message
                                                        // to indicate whether the text was successfully copied.
                                                        $('#copy-button').bind('click', function () {
                                                            var input = document.querySelector('#copy-input');
                                                            input.setSelectionRange(0, input.value.length + 1);
                                                            try {
                                                                var success = document.execCommand('copy');
                                                                if (success) {
                                                                    $('#copy-button').trigger('copied', ['Copied!']);
                                                                } else {
                                                                    $('#copy-button').trigger('copied', ['Copy with Ctrl-c']);
                                                                }
                                                            } catch (err) {
                                                                $('#copy-button').trigger('copied', ['Copy with Ctrl-c']);
                                                            }
                                                        });

                                                        // Handler for updating the tooltip message.
                                                        $('#copy-button').bind('copied', function (event, message) {
                                                            $(this).attr('title', message)
                                                                    .tooltip('fixTitle')
                                                                    .tooltip('show')
                                                                    .attr('title', "Copy to Clipboard")
                                                                    .tooltip('fixTitle');
                                                        });

                                                    });
                                                </script>
                                            </span>
                                        </div>
                                    </form>
                                </div>  
                            </div>

                        </div>
                        <div class="col-xs-12 col-sm-12 col-lg-4 bgWhite">
                            <?php
                            foreach ($videos as $value) {
                                if($video['id']==$value['id']){
                                    continue; // skip video
                                }
                                ?>
                                <div class="col-lg-12 col-sm-12 col-xs-12 bottom-border">
                                    <a href="<?php echo $global['webSiteRootURL']; ?>video/<?php echo $value['clean_title']; ?>" title="<?php echo $value['title']; ?>" class="videoLink">
                                        <div class="col-lg-5 col-sm-5 col-xs-5 nopadding">
                                            <?php
                                                if($value['type']!=="audio"){
                                                    $img = "{$global['webSiteRootURL']}videos/{$value['filename']}.jpg";
                                                }else{
                                                    $img = "{$global['webSiteRootURL']}view/img/mp3.png";
                                                }
                                            ?>
                                            <img src="<?php echo $img; ?>" alt="<?php echo $value['title']; ?>" class="img-responsive" height="130px" />
                                            <span class="glyphicon glyphicon-play-circle"></span>
                                            <span class="duration"><?php echo Video::getCleanDuration($value['duration']); ?></span>
                                        </div>
                                        <div class="col-lg-7 col-sm-7 col-xs-7">
                                            <div class="text-uppercase"><strong><?php echo $value['title']; ?></strong></div>
                                            <div class="details">
                                                <div>
                                                    <?php echo __("Category"); ?>: <?php echo $value['category']; ?>
                                                </div>
                                                <div><?php echo $value['views_count']; ?> <?php echo __("Views"); ?></div>
                                                <div><?php echo $value['creator']; ?></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php
                            }
                            ?> 
                            <ul class="pages">
                            </ul>
                            <script>
                                $(document).ready(function () {
                                    // Total Itens <?php echo $total; ?>

                                    $('.pages').bootpag({
                                        total: <?php echo $totalPages; ?>,
                                        page: <?php echo $_GET['page']; ?>,
                                        maxVisible: 10
                                    }).on('page', function (event, num) {
                                        window.location.replace("<?php echo $global['webSiteRootURL']; ?>page/" + num);
                                    });
                                });
                            </script>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-lg-1"></div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-lg-1"></div>
                        <div class="col-xs-12 col-sm-12 col-lg-10">
                            <?php
                            foreach ($videos as $value) {
                                ?>
                                <div class="col-lg-3 col-sm-12 col-xs-12">
                                    <a href="<?php echo $global['webSiteRootURL']; ?>video/<?php echo $value['clean_title']; ?>" title="<?php echo $value['title']; ?>">
                                        <img src="<?php echo $global['webSiteRootURL']; ?>videos/<?php echo $value['filename']; ?>.jpg" alt="<?php echo $value['title']; ?>" class="img-responsive" height="130px" />
                                        <h2><?php echo $value['title']; ?></h2>
                                        <span class="glyphicon glyphicon-play-circle"></span>
                                        <span class="duration"><?php echo Video::getCleanDuration($value['duration']); ?></span>
                                    </a>
                                </div>
                                <?php
                            }
                            ?> 
                            <ul class="pages">
                            </ul>
                            <script>
                                $(document).ready(function () {
                                    // Total Itens <?php echo $total; ?>

                                    $('.pages').bootpag({
                                        total: <?php echo $totalPages; ?>,
                                        page: <?php echo $_GET['page']; ?>,
                                        maxVisible: 10
                                    }).on('page', function (event, num) {
                                        window.location.replace("<?php echo $global['webSiteRootURL']; ?>page/" + num);
                                    });
                                });
                            </script>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-lg-1"></div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="alert alert-warning">
                    <span class="glyphicon glyphicon-facetime-video"></span> <strong><?php echo __("Warning"); ?>!</strong> <?php echo __("We have not found any videos or audios to show"); ?>.
                </div>
            <?php } ?>  

        </div>
        <?php
        include 'include/footer.php';
        ?>
    </body>
</html>
