<div id="page-wrapper">

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Create New Workorder
                </h1>
                <ol class="breadcrumb">
                    <li class="active">
                        <i class="fa fa-file"></i> Available Workorder Forms
                    </li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-lg-12">
                <?php
                        require_once('./resources/library/dot.php');
                        $dot = new Dot(); // class used for rendering user form related content
                        $dot->renderUserFormsList("createworkorder.php");
                ?>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
<!-- /#page-wrapper -->
