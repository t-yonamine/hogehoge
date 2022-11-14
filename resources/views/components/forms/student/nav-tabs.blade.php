@props([
'data' => [],
'infor'
])
<ul class="nav nav-tabs" role="tablist">
    @if (!empty($data))
    @foreach ( $data as $key => $item)
    <li class="nav-item">
        <a class="nav-link border border-dark" data-toggle="tab" href="#tabs-{{$key}}" role="tab">{{$item['key']}}</a>
    </li>
    @endforeach
    @endif
</ul>
<div class="tab-content">
    <x-forms.student.tabs.infor-st :infor="$infor"></x-forms.student.tabs.infor-st>
    <x-forms.student.tabs.driving-aptitude :infor="$infor"></x-forms.student.tabs.driving-aptitude>
    <x-forms.student.tabs.effect-measurement></x-forms.student.tabs.effect-measurement>
    <x-forms.student.tabs.test-tab></x-forms.student.tabs.test-tab>
    <x-forms.student.tabs.skill-first></x-forms.student.tabs.skill-first>
    <x-forms.student.tabs.skill-second></x-forms.student.tabs.skill-second>
    <x-forms.student.tabs.department-second></x-forms.student.tabs.department-second>
    <x-forms.student.tabs.any-item></x-forms.student.tabs.any-item>
</div>