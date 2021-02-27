<!DOCTYPE html>

    <html lang="ja">
    <head>
    <meta charset="UTF-8">
    <title>mission_5</title>
    </head>
    <body>
    <h1>ミッション5</h1>
    
    パスワード付き<br>
    
    <!--フォームの準備-->
    -------------------------------------追加フォーム--------------------------------<br>
    <form action="" method="post">
        名前      <input type="text" name="namae"    value="<?php echo $edit_data[0]; ?>" placeholder="名前入力"><br>
        コメント  <input type="text" name="comment"  value="<?php echo $edit_data[1]; ?>" placeholder="コメント入力"><br>
        パスワード<input type="text" name="new"                                      placeholder="パスワード" ><br><br>
        
        <input type="hidden"  name="edit_num" value="<?php echo $edit_data[2]; ?>" ><br>
        <input type="submit" name="add"><br>
    </form><br>
    
     -------------------------------------削除フォーム--------------------------------<br>
    <form action="" method="post">
        番号      <input type="text" name="del_num"  placeholder="番号入力(1～)"><br>
        パスワード<input type="text"  name="del_pass" placeholder="パスワード" ><br><br>
        
        <input type="submit" name="del" value="削除"><br>
    </form><br>
    
    -------------------------------------編集番号指定用フォーム--------------------------------<br>
    <form action="" method="post">
        番号      <input type="text" name="edit_num"  placeholder="番号入力(1～)"><br>
        パスワード<input type="text"  name="edit_pass" placeholder="パスワード" ><br><br>
        
        <input type="submit" name="edit" value="編集"><br>
    </form><br>
    
    -------------------------------------表示--------------------------------<br>
    <form action="" method="post">
        <input type="submit" name="output" value="表示"><br>
    </form><br><br><br>
    
    </body>
    </html>

    <?php
    
    /*============================================================================================
                                        DBの接続設定
    =============================================================================================*/
	$dsn = 'mysql:dbname=*********;host=localhost';
	$user = 'tb*******';
	$password = 'NN7*******';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	//=================================テーブルの作成===============================================
	//$sql = 'DROP TABLE tb01';
	//$stmt = $pdo->query($sql);

    $sql = <<<EOM
	CREATE TABLE IF NOT EXISTS tb01(
    id INT AUTO_INCREMENT PRIMARY KEY,
    namae char(32),
    comment varchar(1000),
    date DATETIME,
    password varchar(1000)
    );
EOM;
	$stmt = $pdo->query($sql);
	
	
	//=====================================データ挿入===============================================
	function insert($namae,$comment,$password,$pdo){
	    //$sql = 'DROP TABLE tb01';//「テーブル削除」
		//$stmt = $pdo->query($sql);//「テーブル削除」
		
        $sql = <<<EOM
        INSERT INTO tb01
        (namae, comment, date, password) VALUES (:namae, :comment, :date, :password)
EOM;
        $sql = $pdo -> prepare($sql);
    	$sql -> bindParam(':namae', $namae, PDO::PARAM_STR);
    	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    	$sql -> bindValue(':date', date("Y/m/d H:i:s"), PDO::PARAM_STR);
    	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
    	$sql -> execute();//実行
	}
	
    
    function get_pass($num,$pdo){
        
        $id = (int)$num;
        
        $sql = 'SELECT * FROM tb01 WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        //echo $stmt->rowCount()."個のデータあります!!<br>";// for debug
        
        $results = $stmt->fetchAll(); 
    	foreach ($results as $row){
    		$pass = $row['password'];
    	}
        
        return $pass;
    }
    
    
    /*=====================================================================
                               送信ボタン
    =========================================================================*/
    function send($pdo){
        
        //---------------編集ボタンを押した場合の処理 (新規追加フォームから入力)-----------------
        if(empty($_POST["edit_num"])==false){

            $namae = $_POST["namae"];
            $comment = $_POST["comment"];
            $edit_num = $_POST["edit_num"];
            $new = $_POST["new"];

            $pass = get_pass($edit_num, $pdo);
            
            // パスワードが同じならば
            if($pass == $new){
                
                $id = (int)$edit_num; //変更する投稿番号
                
            	$sql = 'UPDATE tb01 SET namae=:namae,comment=:comment,date=:date, password=:password WHERE id=:id';
            	$stmt = $pdo->prepare($sql);
            	$stmt->bindParam(':namae', $namae, PDO::PARAM_STR);
            	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            	$stmt ->bindValue(':date', date("Y/m/d H:i:s"), PDO::PARAM_STR);
            	$stmt->bindParam(':password', $pass, PDO::PARAM_STR);
            	
            	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            	
            	$stmt->execute();
                
                
                echo "編集完了です!";
                
                
            }else
                echo "パスワードが違います";
            
            
        //--------------------------新規登録の場合--------------------------                   
        }else{
            
            //-----------空欄がないときの処理-----------
            if($_POST["namae"]!="" && $_POST["comment"]!="" && $_POST["new"]!=""){

                //--------変数の準備---------
                $namae = $_POST["namae"]; //名前
                $comment = $_POST["comment"]; //コメント
                $new = $_POST["new"]; //パスワード
                
                insert($namae,$comment,$new,$pdo);
                
                
                
            //-------------空欄が存在する場合--------------
            }else
                echo "空欄がないように入力してください";
        }
    }
    
    /*=====================================================================
                               削除ボタン
    =========================================================================*/
   
    function del($del_num,$pdo){
        
        $id = $del_num;
    	$sql = 'delete from tb01 where id=:id';
    	$stmt = $pdo->prepare($sql);
    	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    	$stmt->execute();
            
        echo "削除完了!<br>";
    }
    
    /*=====================================================================
                               編集ボタン
    =========================================================================*/

    function edit($edit_num,$pdo){
        
        $id = (int)$edit_num;
        
        $sql = 'SELECT * FROM tb01 WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        //echo $stmt->rowCount()."個のデータあります!!<br>";// for debug
        
        $results = $stmt->fetchAll(); 
    	foreach ($results as $row){
    	    $namae = $row['namae'];
    		$comment = $row['comment'];
    	}
        
        return array($namae,$comment,$edit_num);//名前、コメント、番号を既存フォームに送る
    }
    
    
    
    
    /*=====================================================================
                                MAIN
    ========================================================================*/
    
    //-----送信ボタン押下-----
    if(isset($_POST["add"])){
        
        send($pdo);
    }

        
    //-----削除ボタン押下-----
    if(isset($_POST["del"])){
        
        $del_num = $_POST["del_num"];//消す番号
        $del_pass = $_POST["del_pass"];
        
        $pass = get_pass($del_num,$pdo);
        
        if($pass == $del_pass)
            
            del($del_num,$pdo);
        else
            echo "パスワードが違います。";
    }
    
    //-----編集ボタン押下-----
    if(isset($_POST["edit"])){
        
        $edit_num = $_POST["edit_num"];//編集する番号
        $edit_pass = $_POST["edit_pass"];
        
        $pass = get_pass($edit_num,$pdo);
        
        if($pass == $edit_pass)
            
            $edit_data = edit($edit_num,$pdo);
        else
            echo "パスワードが違います。";
    }
    
    //-----表示ボタン押下-----
    if(isset($_POST["output"])){
        
        $sql = 'SELECT * FROM tb01';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        echo $stmt->rowCount()."個のデータあります!!<br>";
        
        $results = $stmt->fetchAll(); 
    	foreach ($results as $row){
    		echo $row['id'].',';
    		echo $row['namae'].',';
    		echo $row['comment'].',';
    		echo $row['date'].',';
    		echo $row['password'].'<br>';
    	echo "<hr>";
    	}
    }

    ?>