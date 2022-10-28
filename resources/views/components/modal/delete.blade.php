@props(['modalTitle' => '消去', 'modalContent' => '削除しますか？'])
<div>
    <div class="modal fade" id="modelDelete" tabindex="-1" role="dialog" aria-labelledby="modelDeleteTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ $modalTitle }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ $modalContent }}
                </div>
                <div class="modal-footer">
                    <form id='formSub' action="" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">はい</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
<script src="{{ asset('/js/tem-script.js') }}" type="text/javascript"></script>
@stop
