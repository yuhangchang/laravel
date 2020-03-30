<table>
    <tr>
        <td>醫事機構名稱</td>
        <td>醫事機構地址</td>
        <td>成人口罩數量</td>
    </tr>
    @foreach($datas as $data)
        <tr>
        @foreach($data as $d)
            <td>{{$d}}</td>
        @endforeach
        <tr>
    @endforeach
</table>
