<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/bootstrap-material-design.min.css">
  <title>Legend of Ikaros</title>

  <style>
  body {
    background: #353535;
    transition: 4s;
    color: white;
  }
  .box {
    position: relative;
    border: 1px solid white;
  }
  .spritebox {
    top: 35vh;
    height: 15vh;
    width: 5vw;
  }
  .writingbox {
    text-align: left;
    padding: 2vh;
    top: 45vh;
    height: 15vh;
    width: 50vw;
  }
  .btn {
    position: fixed;
    top: 42vh;
    left: 42vw;

  }
  .btn-secondary,
  .btn-secondary:hover,
  .btn-secondary:focus {
    color: #333;
    text-shadow: none;
    background-color: #fff;
    border: .05rem solid #fff;
  }
  </style>
</head>
<body>
    <a href="/game/login?reg" class="btn btn-lg btn-secondary">Continue Anyways</a>
  <center>
  <div class=" spritebox"></div>
  <div class="box writingbox"></div>
</center>
  <script src="../assets/pop.min.js"></script>
  <script src="../assets/jquery.slim.min.js"></script>
  <script src="../assets/bootstrap-material-design.min.js"></script>
  <script src="../assets/howler.min.js"></script>
  <script src="../assets/typeit.min.js"></script>
  <script src="../assets/begin.min.js"></script>
</body>
</html>
