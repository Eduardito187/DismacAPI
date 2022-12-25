<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account | <?php echo env("APP_NAME"); ?></title>
</head>
<body>
    <div class="column">
        <div class="row flex center">
            <div class="inline p5">
                <img class="imgLogo" src="{{url('storage/fbnew.png')}}" />
            </div>
            <div class="inline p5">
                <img class="imgLogo" src="{{url('storage/instanew.png')}}" />
            </div>
            <div class="inline p5">
                <img class="imgLogo" src="{{url('storage/ytnew.png')}}" />
            </div>
        </div>
    </div>
</body>
</html>
<style>
    .column{
        width: 100%;
    }
    .row{
        width: 100%;
    }
    .flex{
        display: flex;
    }
    .center{
        text-align: center;
    }
    .p5{
        padding: 5px;
    }
    .inline{
        display: inline-block;
    }
    @media screen and (max-width: 767px) {
        .imgLogo{
            width: 50px;
        }
    }
    @media screen and (min-width: 768px) {
        .imgLogo{
            width: 78px;
        }
    }
</style>