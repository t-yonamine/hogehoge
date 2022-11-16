@props([
'listKey'=> null,
'data'=> null,
'hideButon'=> false,
])
<table class="table table-bordered table-view">
    <thead>
        <tr>
            @foreach($listKey as $item)
            <th> {{ $item['key']}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr>
            @foreach($data as $item)
            <td>
                {{ $item['value']}}
                @if($hideButon)
                <button class="btn btn-secondary">編集</button>
                @endif
            </td>
            @endforeach
        </tr>

    </tbody>
</table>