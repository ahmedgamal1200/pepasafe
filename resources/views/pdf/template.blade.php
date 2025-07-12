<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; }
        .field {
            position: absolute;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div style="position: relative; width: 100%; height: 100%;">
    <img src="{{ $background }}" style="width: 100%; height: 100%;" />

    @foreach ($fields as $field)
        <div
            class="field"
            style="
                    top: {{ $field['y'] }}px;
                    left: {{ $field['x'] }}px;
                    font-family: {{ $field['font'] }};
                    font-size: {{ $field['size'] }}px;
                    color: {{ $field['color'] }};
                "
        >
            {{ $field['text'] }}
        </div>
    @endforeach
</div>
</body>
</html>
