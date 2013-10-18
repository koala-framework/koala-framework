<!DOCTYPE html>
<html>
<head>
    <title>Extensible : Basic Calendar</title>
    <?=$this->assets($this->dep)?>
    <style>
        .sample-ct {
            height: 500px;
        }
    </style>
</head>
<body>
    <h2>Simplest Example</h2>
    <p>This is an example of the most basic CalendarPanel configuration with all default options.  It does not have multiple calendar support
    by default (unless you provide a calendar store) and so all events simply use a deafult color.</p>
    <div id="simple" class="sample-ct"></div>
</body>
</html>
