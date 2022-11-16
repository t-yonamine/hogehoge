@props(['infor', 'disabled' => true])
@php
    $informationAdmision = [
        [
            'key' => '入所年月日', 
            'value' => old('admission_date', $infor->admCheckItem->admission_date?->format('Y-m-d')), 
            'name' => 'admission_date', 'type' => 'date', 'disabled' => $disabled
        ],
        [
            'key' => '教習開始年月日', 
            'value' => old('lesson_start_date', $infor->admCheckItem->lesson_start_date?->format('Y-m-d')), 
            'name' => 'lesson_start_date', 'type' => 'date', 'disabled' => $disabled
        ],
        [
            'key' => '教習期限', 
            'value' => old('lesson_limit', $infor->admCheckItem->lesson_limit?->format('Y/m/d')), 
            'name' => 'lesson_limit', 'type' => 'text', 'disabled' => true
        ],
        [
            'key' => '教習修了年月日', 
            'value' => old('lesson_end_date', $infor->admCheckItem->lesson_end_date?->format('Y/m/d')), 
            'name' => 'lesson_end_date', 'type' => 'text', 'disabled' => true
        ],
    
        [
            'key' => '検定期限', 
            'value' => old('test_limit', $infor->admCheckItem->test_limit?->format('Y/m/d')), 
            'name' => 'test_limit', 'type' => '', 'disabled' => true
        ],
        [
            'key' => '修了証明書発行年月日', 
            'value' => old('issue_date', $infor?->cerificateOfCompletion?->issue_date?->format('Y/m/d')), 
            'name' => 'issue_date', 'type' => 'text', 'disabled' => true
        ],
        [
            'key' => '修了証明書番号', 
            'value' => old('cert_num', $infor?->cerificateOfCompletion?->cert_num), 
            'name' => 'cert_num', 'type' => 'text', 'disabled' => true
        ],
        [
            'key' => '卒業年月日', 
            'value' => old('issue_date', $infor?->cerificateGraduation?->issue_date?->format('Y/m/d')), 
            'name' => '', 'type' => 'text', 'disabled' => true
        ],
        [
            'key' => '卒業証明書番号', 
            'value' => old('cert_num', $infor->cerificateGraduation?->cert_num), 
            'name' => 'cert_num', 'type' => 'text', 'disabled' => true
        ],
    
        [
            'key' => '転出年月日', 
            'value' => old('moving_out_date', $infor->admCheckItem->moving_out_date?->format('Y-m-d')), 
            'name' => 'moving_out_date', 'type' => 'date', 'disabled' => $disabled
        ],
        [
            'key' => '転入年月日', 
            'value' => old('moving_in_date', $infor->admCheckItem->moving_in_date?->format('Y-m-d')), 
            'name' => 'moving_in_date', 'type' => 'date', 'disabled' => $disabled
        ],
        [
            'key' => '転退所年月日', 
            'value' => old('discharge_date', $infor->admCheckItem->discharge_date?->format('Y-m-d')), 
            'name' => 'discharge_date', 'type' => 'date', 'disabled' => $disabled
        ],
    ];
    
    $posseionInformation = [
        [
            'key' => '交付年月日', 
            'value' => old('lic_issue_date', $infor->admCheckItem->lic_issue_date?->format('Y-m-d')), 
            'name' => 'lic_issue_date', 'type' => 'date', 'disabled' => $disabled
        ], 
        [
            'key' => '有効期限', 
            'value' => old('lic_expy_date', $infor->admCheckItem->lic_expy_date?->format('Y-m-d')), 
            'name' => 'lic_expy_date', 'type' => 'date', 'disabled' => $disabled
        ], 
        [
            'key' => '免許証番号', 
            'value' => old('lic_num', $infor->admCheckItem->lic_num), 
            'name' => 'lic_num', 'type' => 'text', 'disabled' => $disabled
        ], 
        [
            'key' => '管轄警察署', 
            'value' => old('lic_psc_name', $infor->admCheckItem->lic_psc_name), 
            'name' => 'lic_psc_name', 'type' => 'text', 'disabled' => true
            ]
    ];
    
    $provisionalLicense = [
        [
            'key' => '大型',
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::PL_MVL]),
            'name' => 'pl_mvl',
        
        ],
        [
            'key' => '中型',
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::PM_MVL]),
            'name' => 'pm_mvl',
        ],
        [
            'key' => '準中型',
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::PSM_MVL]),
            'name' => 'psm_mvl',
        ],
        [
            'key' => '普通',
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::PS_MVL_MT]),
            'name' => 'ps_mvl_mt',
        ],
    ];
    
    $classTwoLicense = [
        [
            'key' => '大型二', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::L_MVL_2]), 
            'name' => 'l_mvl_2'
        ], 
        [
            'key' => '中型二', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::M_MVL_2]), 
            'name' => 'm_mvl_2'
        ], 
        [
            'key' => '普通二', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::S_MVL_2]), 
            'name' => 's_mvl_2'
        ], 
        [
            'key' => '大特二', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::SL_MVL_2]), 
            'name' => 'sl_mvl_2'
        ], 
        [
            'key' => '牽引二', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::TOWING_2]), 
            'name' => 'towing_2'
            ]
    ];
    
    $oneClassLicense = [
        [
            'key' => '大型', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::L_MVL, App\Enums\LicenseType::L_MVL_L]), 
            'name' => 'l_mvl'
        ],
        [
            'key' => '中型', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::M_MVL, App\Enums\LicenseType::M_MVL_L]), 
            'name' => 'm_mvl'
        ],
        [
            'key' => '準中型', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::SM_MVL, App\Enums\LicenseType::SM_MVL_L]), 
            'name' => 'sm_mvl'
        ],
        [
            'key' => '普通', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::S_MVL_MT, App\Enums\LicenseType::S_MVL_MT_L, App\Enums\LicenseType::S_MVL_AT]), 
            'name' => 's_mvl_mt'
        ],
        [
            'key' => '大特', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::SL_MVL, App\Enums\LicenseType::SL_MVL_L]), 
            'name' => 'sl_mvl'
        ],
        [
            'key' => '普自二', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::L_ML, App\Enums\LicenseType::L_ML_L, App\Enums\LicenseType::L_ML_AT]), 
            'name' => 'l_ml'
        ],
        [
            'key' => '小型', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::S_ML, App\Enums\LicenseType::S_ML_L, App\Enums\LicenseType::S_ML_AT]), 
            'name' => 's_ml'
        ],
        [
            'key' => '原付', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::SS_MVL, App\Enums\LicenseType::SS_MVL_L]), 
            'name' => 'ss_mvl'
        ],
        [
            'key' => '牽引', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::MBL]), 
            'name' => 'mbl'
        ],
        [
            'key' => '大自二', 
            'value' => App\Models\AdmCheckItem::checkExistLicense($infor->admCheckItem->curLicTypes, [App\Enums\LicenseType::TOWING, App\Enums\LicenseType::TOWING_L]), 
            'name' => 'towing'
        ],
    ];
    
@endphp
<div class="tab-pane active" id="tabs-0" role="tabpanel">
    {{-- Block C --}}
    <h5 class="mt-2 mb-2">入所時の記録</h5>
    <table class="table table-bordered table-view">
        <tbody>
            @foreach ($informationAdmision as $item)
                <tr>
                    <th class="w-20">{{ $item['key'] }}</th>
                    <td>
                        <x-pc.forms.input name='{{ $item["name"] }}' type='{{ $item["type"] ?? "text" }}'
                            disabled='{{ $item["disabled"] }}' value='{{ $item["value"] }}' />
                    </td>
                </tr>
            @endforeach

            @if (count($infor->cerificateProvisionalLicense) == 0)
                <tr>
                    <th class="w-20">仮免許交付年月日1</th>
                    <td>
                        <input type="text" class="form-control" disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">仮免許有効期限1</th>
                    <td>
                        <input type="text" class="form-control"disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">仮免許証番号1</th>
                    <td>
                        <input type="text" class="form-control" disabled>
                    </td>
                </tr>
            @else
                @foreach ($infor->cerificateProvisionalLicense as $index => $item)
                <tr>
                    <th class="w-20">{{ '仮免許交付年月日' . $index + 1 }}</th>
                    <td>
                        <x-pc.forms.input disabled='true' value='{{ $item->issue_date?->format("Y-m-d") }}' />
                    </td>
                </tr>
                <tr>
                    <th class="w-20">{{ '仮免許有効期限' . $index + 1 }}</th>
                    <td>
                        <x-pc.forms.input disabled='true' value='{{ $item->expy_date?->format("Y-m-d") }}' />
                    </td>
                </tr>
                <tr>
                    <th class="w-20">{{ '仮免許証番号' . $index + 1 }}</th>
                    <td>
                        <x-pc.forms.input disabled='true' value='{{ $item->cert_num }}' />
                    </td>
                </tr>
                @endforeach
            @endif
           
        </tbody>
    </table>
    {{-- Block C END --}}

    {{-- Block D --}}
    <h5 class="mt-2 mb-2">所持免許情報</h5>
    <table class="table table-bordered table-view">
        <tbody>
            @foreach ($posseionInformation as $item)
                <tr>
                    <th class="w-20">{{ $item['key'] }}</th>
                    <td>
                        <x-pc.forms.input name='{{ $item["name"] }}' type='{{ $item["type"] ?? "text" }}'
                            disabled='{{ $item["disabled"] }}' value='{{ $item["value"] }}' />
                    </td>
                </tr>
            @endforeach
            <tr>
                <th class="w-20">仮免許</th>
                <td>
                    @foreach ($provisionalLicense as $item)
                        <div class="form-check m-2">
                            <input name="{{ $item['name'] }}" class="form-check-input" type="checkbox"
                                @disabled($disabled)
                                @checked(old($item['name'], $item['value'] ? 'on' : '') == 'on')>
                            <label class="form-check-label">
                                {{ $item['key'] }}
                            </label>
                        </div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="w-20">一種免許</th>
                <td>
                    @foreach ($oneClassLicense as $item)
                        <div class="form-check m-2">
                            <input name="{{ $item['name'] }}" class="form-check-input" type="checkbox"
                                @disabled($disabled)
                                @checked(old($item['name'], $item['value'] ? 'on' : '') == 'on')>
                            <label class="form-check-label">
                                {{ $item['key'] }}
                            </label>
                        </div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="w-20">二種免許</th>
                <td>
                    @foreach ($classTwoLicense as $item)
                        <div class="form-check m-2">
                            <input name="{{ $item['name'] }}" class="form-check-input" type="checkbox"
                                @disabled($disabled)
                                @checked(old($item['name'], $item['value'] ? 'on' : '') == 'on')>
                            <label class="form-check-label">
                                {{ $item['key'] }}
                            </label>
                        </div>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
    {{-- Block D End --}}

    {{-- Block E --}}
    <h5 class="mt-2 mb-2">教習条件</h5>
    <table class="table table-bordered table-view">
        <tbody>
            <tr>
                <th class="w-20">教習条件</th>
                <td>
                    <div class="form-check m-2">
                        <input name="lesson_cond_glasses" class="form-check-input " type="checkbox"
                            @disabled($disabled)
                            @checked(old('lesson_cond_glasses', $infor->admCheckItem->lesson_cond_glasses))>
                        <label class="form-check-label">
                            眼鏡等
                        </label>
                    </div>
                    <div class="form-check m-2">
                        <input name="lesson_cond_contact_lens" class="form-check-input " type="checkbox"
                            @disabled($disabled)
                            @checked(old('lesson_cond_contact_lens', $infor->admCheckItem->lesson_cond_contact_lens))>
                        <label class="form-check-label">
                            コンタクト
                        </label>
                    </div>
                    <div class="form-check m-2">
                        <input name="lesson_cond_hearing_aid" class="form-check-input " type="checkbox"
                            @disabled($disabled)
                            @checked(old('lesson_cond_hearing_aid', $infor->admCheckItem->lesson_cond_hearing_aid))>
                        <label class="form-check-label">
                            補聴器
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>応急救護</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" maxlength="16"
                                name="first_aid_crse_exempt_sw" id="first_aid_crse_exempt_sw1" value="0"
                                @disabled($disabled)
                                @checked(old('first_aid_crse_exempt_sw', $infor->admCheckItem->first_aid_crse_exempt_sw) == 0) />
                            <label class="form-check-label" for="first_aid_crse_exempt_sw1">
                                有
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="first_aid_crse_exempt_sw"
                                @disabled($disabled)
                                id="first_aid_crse_exempt_sw2" value="1" @checked(old('first_aid_crse_exempt_sw', $infor->admCheckItem->first_aid_crse_exempt_sw)  == 1)>
                            <label class="form-check-label" for="first_aid_crse_exempt_sw2">
                                無
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>応急救護(確認)</th>
                <td>
                    <x-pc.forms.input name='first_aid_crse_exempt_txt' maxlength='40' disabled='{{$disabled}}'
                        value='{{ old("first_aid_crse_exempt_txt", $infor->admCheckItem->first_aid_crse_exempt_txt) }}' />
                </td>
            </tr>
        </tbody>
    </table>
    {{-- Block E END --}}
    {{-- block F 入所時身体的適性検査 --}}
    <h5 class="mt-2 mb-2">入所時身体的適性検査</h5>
    <table class="table table-bordered table-view">
        <tbody>
            <tr>
                <th class="w-20">視力裸眼（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <x-pc.forms.input name='eyesight_naked_left_1' type='text' disabled='{{$disabled}}'
                                value='{{ old("eyesight_naked_left_1", $infor->aptitudePhyFirst1->eyesight_naked_left) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='eyesight_naked_right_1' type='text' disabled='{{$disabled}}'
                                value='{{ old("eyesight_naked_right_1", $infor->aptitudePhyFirst1->eyesight_naked_right) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='eyesight_naked_both_1' type='text' disabled='{{$disabled}}'
                                value='{{ old("eyesight_naked_both_1", $infor->aptitudePhyFirst1->eyesight_naked_both) }}' />
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">視力矯正（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <x-pc.forms.input name='eyesight_correct_left_1' type='text' disabled='{{$disabled}}'
                                value='{{ old("eyesight_correct_left_1", $infor->aptitudePhyFirst1->eyesight_correct_left) }}' />
                        </div>
                        <div class="col"> 
                            <x-pc.forms.input name='eyesight_correct_right_1' type='text' disabled='{{$disabled}}'
                                value='{{ old("eyesight_correct_right_1", $infor->aptitudePhyFirst1->eyesight_correct_right) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='eyesight_correct_both_1' type='text' disabled='{{$disabled}}'
                                value='{{ old("eyesight_correct_both_1", $infor->aptitudePhyFirst1->eyesight_correct_both) }}' />
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">視野（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <x-pc.forms.input name="field_of_view_left_1" type="text" disabled='{{$disabled}}'
                                value='{{ old("field_of_view_left_1", $infor->aptitudePhyFirst1->eyesight_correct_left) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name="field_of_view_right_1" type="text" disabled='{{$disabled}}'
                                value='{{ old("field_of_view_right_1", $infor->aptitudePhyFirst1->field_of_view_right) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name="field_of_view_both_1" type="text" disabled='{{$disabled}}'
                                value='{{ old("field_of_view_both_1", $infor->aptitudePhyFirst1->field_of_view_both) }}' />
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>色彩識別</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="color_discimination_cd_1" @disabled($disabled)
                                id="color_discimination_cd_11" value="0" @checked(old('color_discimination_cd_1', $infor->aptitudePhyFirst1->color_discimination_cd) == 0)>
                            <label class="form-check-label" for="color_discimination_cd_11">
                                適
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="color_discimination_cd_1" @disabled($disabled)
                                id="color_discimination_cd_12" value="1" @checked(old('color_discimination_cd_1', $infor->aptitudePhyFirst1->color_discimination_cd) == 1)>
                            <label class="form-check-label" for="color_discimination_cd_12">
                                否
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>聴力</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="hearing_cd_1" id="hearing_cd_11" @disabled($disabled)
                                value="0" @checked(old('hearing_cd_1', $infor->aptitudePhyFirst1->hearing_cd) == 0)>
                            <label class="form-check-label" for="hearing_cd_11">
                                第１号
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="hearing_cd_1" id="hearing_cd_12" @disabled($disabled)
                                value="1" @checked(old('hearing_cd_1', $infor->aptitudePhyFirst1->hearing_cd) == 1)>
                            <label class="form-check-label" for="hearing_cd_12">
                                第２号
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>運動能力</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="athletic_ability_cd_1" @disabled($disabled)
                                id="athletic_ability_cd_11" value="0" @checked(old('athletic_ability_cd_1', $infor->aptitudePhyFirst1->athletic_ability_cd) == 0)>
                            <label class="form-check-label" for="athletic_ability_cd_11">
                                適
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="athletic_ability_cd_1" @disabled($disabled)
                                id="athletic_ability_cd_12" value="1" @checked(old('athletic_ability_cd_1', $infor->aptitudePhyFirst1->athletic_ability_cd) == 1)>
                            <label class="form-check-label" for="athletic_ability_cd_12">
                                否
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>検査日</th>
                <td>
                    <input class="form-control" name="confirm_date_1" type="date" @disabled($disabled)
                        value="{{ old('confirm_date_1', $infor->aptitudePhyFirst1->getConfirmRec()->confirm_date?->format('Y-m-d')) }}">
                </td>
            </tr>
            <tr>
                <th>検査者</th>
                <td>
                    <select class="form-control" name="staff_id_1" @disabled($disabled)>
                        <option></option>
                        @foreach($infor->testerList as $tester)
                        <option value="{{$tester->id}}" @selected(old('staff_id_1', $infor->aptitudePhyFirst1?->getConfirmRec()->staff_id ?? '') == $tester->id)>{{$tester->name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    {{-- Block F 1-14 END --}}

    {{-- Block F 修了検定時時身体的適性検査  --}}
    <h5 class="mt-2 mb-2">修了検定時時身体的適性検査</h5>
    <input hidden name="gaptitude_phys_id_1" value="{{$infor->aptitudePhyFirst1->id}}">
    <input hidden name="gaptitude_phys_id_2" value="{{$infor->aptitudePhyFirst2->id}}">
    <table class="table table-bordered table-view">
    <table class="table table-bordered table-view">
        <tbody>
            <tr>
                <th class="w-20">視力裸眼（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <x-pc.forms.input name='eyesight_naked_left_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("eyesight_naked_left_2", $infor->aptitudePhyFirst2->eyesight_naked_left) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='eyesight_naked_right_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("eyesight_naked_right_2", $infor->aptitudePhyFirst2->eyesight_naked_right) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='eyesight_naked_both_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("eyesight_naked_both_2", $infor->aptitudePhyFirst2->eyesight_naked_both) }}' />
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">視力矯正（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <x-pc.forms.input name='eyesight_correct_left_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("eyesight_correct_left_2", $infor->aptitudePhyFirst2->eyesight_correct_left) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='eyesight_correct_right_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("eyesight_correct_right_2", $infor->aptitudePhyFirst2->eyesight_correct_right) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='eyesight_correct_both_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("eyesight_correct_both_2", $infor->aptitudePhyFirst2->eyesight_correct_both) }}' />
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">視野（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <x-pc.forms.input name='field_of_view_left_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("field_of_view_left_2", $infor->aptitudePhyFirst2->field_of_view_left) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='field_of_view_right_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("field_of_view_right_2", $infor->aptitudePhyFirst2->field_of_view_right) }}' />
                        </div>
                        <div class="col">
                            <x-pc.forms.input name='field_of_view_both_2' type='text' disabled="{{$disabled}}"
                                value='{{ old("field_of_view_both_2", $infor->aptitudePhyFirst2->field_of_view_both) }}' />
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>色彩識別</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="color_discimination_cd_2" @disabled($disabled)
                                id="color_discimination_cd_21" value="0" @checked(old('color_discimination_cd_2', $infor->aptitudePhyFirst2->color_discimination_cd ?? 0) == 0)>
                            <label class="form-check-label" for="color_discimination_cd_21">
                                適
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="color_discimination_cd_2" @disabled($disabled)
                                id="color_discimination_cd_22" value="1" @checked(old('color_discimination_cd_2', $infor->aptitudePhyFirst2->color_discimination_cd) == 1)>
                            <label class="form-check-label" for="color_discimination_cd_22">
                                否
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>聴力</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="hearing_cd"_2 id="hearing_cd_21" @disabled($disabled)
                                value="0" @checked(old('hearing_cd_2', $infor->aptitudePhyFirst2->hearing_cd ?? 0) == 0)>
                            <label class="form-check-label" for="hearing_cd_21">
                                第１号
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="hearing_cd_2" id="hearing_cd_22" @disabled($disabled)
                                value="1" @checked(old('hearing_cd_2', $infor->aptitudePhyFirst2->hearing_cd) == 1)>
                            <label class="form-check-label" for="hearing_cd_22">
                                第２号
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>運動能力</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="athletic_ability_cd_2" @disabled($disabled)
                                id="athletic_ability_cd_21" value="0" @checked(old('athletic_ability_cd_2', $infor->aptitudePhyFirst2->athletic_ability_cd ?? 0) == 0)>
                            <label class="form-check-label" for="athletic_ability_cd_21">
                                適
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="athletic_ability_cd_2" @disabled($disabled)
                                id="athletic_ability_cd_22" value="1" @checked(old('athletic_ability_cd_2', $infor->aptitudePhyFirst2->athletic_ability_cd) == 1)>
                            <label class="form-check-label" for="athletic_ability_cd_22">
                                否
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>検査日</th>
                <td>
                    <x-pc.forms.input name='confirm_date_2' type='date' disabled="{{$disabled}}"
                            value='{{ old("confirm_date_2", $infor->aptitudePhyFirst2->getConfirmRec()->confirm_date?->format("Y-m-d")) }}' />
                </td>
            </tr>
            <tr>
                <th>検査者</th>
                <td>
                    @if($disabled)
                    <x-pc.forms.input name='staff_id_2' type='date'
                            value='{{ old("staff_id_2", $infor->aptitudePhyFirst2?->getConfirmRec()->staff_name)}}' />
                    @else
                    <select class="form-control" name="staff_id_2">
                        <option></option>
                        @foreach($infor->testerList as $tester)
                        <option value="{{$tester->id}}" @selected(old('staff_id_2', $infor->aptitudePhyFirst2?->getConfirmRec()->staff_id ?? '') == $tester->id)>{{$tester->name}}</option>
                        @endforeach
                    </select>
                    @endif
                    
                </td>
            </tr>
        </tbody>
    </table>
    {{-- Block F 15-28 END --}}
</div>
@section('js')
    <script>
        var policeDepartment = @json($infor->policeDepartment);
        var lessonLimitMonth = '{{ $infor->lessonLimitMonth->sv_value }}';
        $('input[name="lesson_start_date"]').on('change', function() {
            var startDate = new Date(this.value);
            startDate.setMonth(startDate.getMonth() + parseInt(lessonLimitMonth));
            startDate.setDate(startDate.getDate() - 1);
            $('input[name="lesson_limit"]').val(startDate.toISOString().split('T')[0].replaceAll('-', '/'));

        });

        $('input[name="other_check_sw"]').on('change', function() {
            $('input[name="other_check_text"]').prop('disabled', !this.checked).val('');
        })

        $('input[name="lic_num"]').on('change', function() {
            const licNum = this.value;
            let licName = policeDepartment.find(rs => rs.cd_value == licNum)?.cd_text;
            $('input[name="lic_psc_name"]').val(licName);
        })
    </script>
@stop
