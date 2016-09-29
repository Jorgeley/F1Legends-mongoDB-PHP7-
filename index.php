<!DOCTYPE html>
<!--
    Simples projeto exemplo CRUD usando as tecnologias mongoDB + Bootstrap + PHP7
    *comentários/sugestões são bem vindas
    Sample simple CRUD project using the technologies mongoDB + Boostsrap + PHP7
    *comments/suggestions are welcome
                        jorgeley@gmail.com / https://github.com/jorgeley
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="F1.ico">
        <title>F1 Legends</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/signin.css">
        <link rel="stylesheet" href="css/grid.css">
    </head>
    
    <body>
        <div class="container">
            
            <?php
                //bootstrap
                require 'vendor/autoload.php';                
                try{
                    $mongo = new MongoDB\Client("mongodb://localhost");
                }catch (MongoDB\Client\Exception $exception){
                    error($exception);
                }
                $drivers = $mongo->f1Legends->driver; //the collection
                
                //search for a driver by his id or delete him
                if ($_GET['id'] ?? null)
                    if ($_GET['op'] == 'v')
                        try {
                            $driver = $drivers->findOne([ //search for a driver by his id
                                                    '_id' => new MongoDB\BSON\ObjectId($_GET['id'])
                            ]);
                        }catch(MongoDB\Driver\Exception\ConnectionTimeoutException $exception){
                            error($exception);
                        }catch(MongoDB\Driver\Exception\InvalidArgumentException $exception){
                            error($exception);
                        }
                    elseif ($_GET['op'] == 'x')
                        try{
                            $drivers->deleteOne([ //delete the driver by his id
                                            '_id' => new MongoDB\BSON\ObjectId($_GET['id'])
                            ]);
                        }catch(MongoDB\Driver\Exception\ConnectionTimeoutException $exception){
                            error($exception);
                        }catch(MongoDB\Driver\Exception\InvalidArgumentException $exception){
                            error($exception);
                        }

                //add or update new driver
                if ($_POST)
                    if ($_POST['id'] ?? null)
                        try{
                            $drivers->updateOne( //updates the driver
                                            ['_id' => new MongoDB\BSON\ObjectId($_POST['id'])], //who to update?
                                            ['$set' => [ //new data
                                                    'name' => $_POST['name'],
                                                    'team' => $_POST['team'] ]
                                            ]);
                        }catch(MongoDB\Driver\Exception\ConnectionTimeoutException $exception){
                            error($exception);
                        }catch(MongoDB\Driver\Exception\InvalidArgumentException $exception){
                            error($exception);
                        }
                    else
                        try{
                            $drivers->insertOne([ //add new driver
                                        'name' => $_POST['name'],
                                        'team' => $_POST['team']
                            ]);
                        }catch(MongoDB\Driver\Exception\ConnectionTimeoutException $exception){
                            error($exception);
                        }
            ?>
            
            <!-- drivers CRUD form -->
            <form class="form-signin" method="post" action="?">
                <a href="?" style="text-decoration:none">
                    <img src="F1.png" style="float:left">
                    <h2 class="form-signin-heading">Legends</h2>
                </a>
                <input type="hidden" name="id" value="<?=$driver->_id ?? null?>">
                <label for="name" class="sr-only">Piloto</label>
                <input class="form-control" type="text" name="name" id="name" placeholder="piloto / driver" required value="<?=$driver->name ?? null?>">
                <label for="team" class="sr-only">Equipe</label>
                <input class="form-control" type="text" name="team" id="team" placeholder="equipe / team" value="<?=$driver->team ?? null?>">
                <button class="btn btn-lg btn-primary btn-block" type="submit">save</button>
                <i>mongoDB+PHP7</i>
            </form>
            
            <!-- drivers table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Piloto / Driver</th>
                            <th>Equipe / Team</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody><?php
                        //list the drivers
                        try{
                            foreach ($drivers->find() as $driver){?>
                                <tr>
                                  <td><?=$driver->name?></td>
                                  <td><?=$driver->team?></td>
                                  <td>
                                      <a href="?op=v&id=<?=$driver->_id?>">...</a> | 
                                      <a href="?op=x&id=<?=$driver->_id?>">X</a>
                                  </td>
                                </tr><?php
                            }                            
                        }catch(MongoDB\Driver\Exception\ConnectionTimeoutException $exception){
                            error($exception);
                        }?>
                    </tbody>
                </table>
            </div>
                  
        </div>
        
    </body>
    
</html>

<?php
    //just an auxiliar function to show exceptions
    function error($exception){
        echo "<h3>:( Sorry </h3>", $exception->getMessage(), "\n"
            ."In file: ", $exception->getFile(), "\n"
            ."On line: ", $exception->getLine(), "\n";
    }
?>