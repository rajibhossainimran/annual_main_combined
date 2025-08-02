import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import Select from 'react-select';
import InputSearch from '../../componants/InputSearch';
import ModalComponent from '../../componants/ModalComponent';
import Swal from 'sweetalert2'
import { PatternFormat } from 'react-number-format';
import moment from 'moment';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Paginate from '../../componants/Paginate';
// /all_csr_completed_tender_list
export default function CsrCoverLetter() {
    const [TenderId, setTenderId] = useState('')
    const [Details, setDetails] = useState('')
    const [PresidentOptions, setPresidentOptions] = useState('')
    const [PresidentList, setPresidentList] = useState('')
    const [MemberList, setMemberList] = useState('')
    const [CoOperativeMemberList, setCoOperativeMemberList] = useState('')
    const [MemberOptions, setMemberOptions] = useState('')
    const [CoOperativeMemberOptions, setCoOperativMemberOptions] = useState('')
    const [isFormSubmited, setIsFormSubmited] = useState(false)

    const handleChangeTender = (value) => {
        setTenderId(value.value);

        if (value.data) {
            let userListoptions = [];
            value.data.tender_csr.forEach(csr => {
                csr.csr_pvms_approval.forEach(each_approval => {
                    let findItem = userListoptions.find(i => i.value == each_approval.approved_by.id);
                    if (!findItem) {
                        debugger
                        userListoptions.unshift({ value: each_approval.approved_by.id, label: each_approval.approved_by.name, data: each_approval.approved_by });
                    }
                });
            });
            console.log(userListoptions);
            setPresidentOptions(userListoptions);
            setMemberOptions(userListoptions);
            setCoOperativMemberOptions(userListoptions);
        }

    }

    const handleChangePresident = (value, select) => {
        if (select.action === "remove-value" && select.removedValue) {
            setPresidentList(prev => prev.filter(i => i.value !== select.removedValue.value))
        } else if (select.action === "clear") {
            setPresidentList('')
        } else if (select.action === "select-option" && select.option) {
            setPresidentList(prev => {
                let copy = [...prev]
                let findIndexExist = copy.findIndex(i => i == select.option.value);
                if (findIndexExist < 0) {
                    copy.push(select.option)
                }

                return copy;
            })
        }
    }

    const handleChangeMember = (value, select) => {
        if (select.action === "remove-value" && select.removedValue) {
            setMemberList(prev => prev.filter(i => i.value !== select.removedValue.value))
        } else if (select.action === "clear") {
            setMemberList('')
        } else if (select.action === "select-option" && select.option) {
            setMemberList(prev => {
                let copy = [...prev]
                let findIndexExist = copy.findIndex(i => i == select.option.value);
                if (findIndexExist < 0) {
                    copy.push(select.option)
                }

                return copy;
            })
        }
    }

    const handleSaveCoverLetter = () => {
        // /save-cover-letter
        if (!Details || !PresidentList || !MemberList || !CoOperativeMemberList || (PresidentList && PresidentList.length == 0) || (MemberList && MemberList.length == 0) || (CoOperativeMemberList && CoOperativeMemberList.length == 0)) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: 'Deatils, President, Member, Co-operative Members Field is Required',
                // footer: '<a href="">Why do I have this issue?</a>'
            })
            window.scroll(0, 0);
            return;
        }
        setIsFormSubmited(true);
        let data = {
            'tender': TenderId,
            'details': Details,
            'president': PresidentList,
            'member': MemberList,
            'co_operative_member': CoOperativeMemberList,
        };

        axios.post(window.app_url + '/save-cover-letter', data).then((res) => {
            setIsFormSubmited(false)
            window.location.href = window.cover_letter
        }).catch(() => {
            setIsFormSubmited(false)
        })
    }

    const handleChangeCoOperativeMember = (value, select) => {
        if (select.action === "remove-value" && select.removedValue) {
            setCoOperativeMemberList(prev => prev.filter(i => i.value !== select.removedValue.value))
        } else if (select.action === "clear") {
            setCoOperativeMemberList('')
        } else if (select.action === "select-option" && select.option) {
            setCoOperativeMemberList(prev => {
                let copy = [...prev]
                let findIndexExist = copy.findIndex(i => i == select.option.value);
                if (findIndexExist < 0) {
                    copy.push(select.option)
                }

                return copy;
            })
        }
    }

    const loadOptions = (inputValue, callback) => {
        axios.get(window.app_url + '/all_csr_completed_tender_list?keyword=' + inputValue).then((res) => {
            const data = res.data;

            let option = [];
            for (const iterator of data) {
                option.push({ value: iterator.id, label: iterator.tender_no, data: iterator })
            }

            callback(option);
        })
    };

    return (
        <div>
            <div className='row px-2 py-4'>
                <div className='col-md-4 gap-2'>
                    <b className='mb-2'>Search Tender (All CSR Completed)</b>
                    <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleChangeTender} defaultOptions placeholder="Search Tender" />
                </div>
                {TenderId && <>
                    <div className='col-md-12 pt-2'>
                        <b>Details: </b>
                        <CKEditor
                            editor={ClassicEditor}
                            config={{
                                toolbar: ['undo', 'redo',
                                    '|', 'heading',
                                    '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                                    '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                                    '|', 'blockQuote', 'insertTable', 'underline',
                                    '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', 'alignment']
                            }}
                            data={Details}
                            onChange={(event, editor) => {
                                const data = editor.getData();
                                setDetails(data)
                            }}
                        />
                    </div>
                    <div className='col-md-6 pt-2'>
                        <b>President: </b>
                        <Select
                            className="basic-single"
                            classNamePrefix="select"
                            name="president"
                            options={PresidentOptions}
                            isMulti
                            value={PresidentList}
                            onChange={handleChangePresident}
                        />
                    </div>
                    <div className='col-md-6 pt-2'>
                        <b>Member: </b>
                        <Select
                            className="basic-single"
                            classNamePrefix="select"
                            name="member"
                            options={MemberOptions}
                            isMulti
                            value={MemberList}
                            onChange={handleChangeMember}
                        />
                    </div>
                    <div className='col-md-6 pt-2'>
                        <b>Co-Operated Member: </b>
                        <Select
                            className="basic-single"
                            classNamePrefix="select"
                            name="co_member"
                            options={CoOperativeMemberOptions}
                            isMulti
                            value={CoOperativeMemberList}
                            onChange={handleChangeCoOperativeMember}
                        />
                    </div>
                    <div className='col-md-12 pt-2'>
                        <button className="btn btn-success"
                            disabled={isFormSubmited} onClick={() => handleSaveCoverLetter()}
                        >
                            <>{isFormSubmited ? `Save...` : `Save`}</>
                        </button>
                    </div>
                </>}
            </div>
        </div>
    )
}

if (document.getElementById('react-csr-cover-letter')) {
    createRoot(document.getElementById('react-csr-cover-letter')).render(<CsrCoverLetter />)
}
