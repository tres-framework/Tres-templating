<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        
        <title>Home - Tres templating</title>
        
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <style>
        body {
            font: 13pt Arial, sans-serif;
            padding: 64px 128px;
        }
        </style>
    </head>
    <body>
        <h1>Tres templating engine</h1>
        <p>
            {{-- This is supposed to be a comment. --}}
            {{--
            This is supposed to be
            another comment.
            --}}
            
            @if(($x == 17 && ($x - 1 == 16 + 1 - 1)))
                Check!<br />
            @endif
            
            @if($x == 17)
                @foreach($array as $k => $v)
                    {{ $k }} = {{ $v }}<br />
                @endforeach
                
                X is equal to 17.<br />
            @endif
            
            {{ $y }}<br />
            {{ $z or 'fallback' }}<br />
            
            <h2>{{ 'Raw echo' }}</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <br />
            
            {{! $html }}
            
            <hr />
            
            @for($i = 1; $i <= 5; $i++)
                Your number is: {{ $i }}<br />
            @endfor
        </p>
    </body>
</html>
 
