import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import InputSearch from '../../componants/InputSearch';
import ModalComponent from '../../componants/ModalComponent';
import Swal from 'sweetalert2'
import { PatternFormat } from 'react-number-format';
import moment from 'moment';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import DatePicker from "react-datepicker";

import "react-datepicker/dist/react-datepicker.css";

export default function CreateEditNoteSheet() {
    const [Page, setPage] = useState(1)
    const [Demands, setDemands] = useState()
    const [DemandsLinks, setDemandsLinks] = useState()
    const [IsLoading, setIsLoading] = useState(true)
    const [IsShowModal, setIsShowModal] = useState(false)
    const [IsShowCreateNoteSheetModal, setIsShowCreateNoteSheetModal] = useState(false)
    const [DemandDetailsItem, setDemandDetailsItem] = useState()
    const [NotesheetUniqDemandPvmsList, setNotesheetUniqDemandPvmsList] = useState()
    const [NotesheetDemandList, setNotesheetDemandList] = useState()
    const [NoteSheetDemandItemType, setNoteSheetDemandItemType] = useState()
    const [ItemTypeList, setItemTypeList] = useState()
    const [isFormSubmited, setIsFormSubmited] = useState(false)
    const [NotesheetDate, setNotesheetDate] = useState()
    const [Notesheetno, setNotesheetno] = useState()
    const [NotesheetNoError, setNotesheetNoError] = useState('')
    const [NotesheetBudget, setNotesheetBudget] = useState('')
    const [NotesheetDetails, setNotesheetDetails] = useState('<p>১।&nbsp; &nbsp;পতাকা সংলগ্নী *** </p><p>২।&nbsp; &nbsp;সশস্ত্র বাহিনীর সদস্যদের জন্য নিম্নবর্ণিত ঔষধসমুহ জরুরী প্রয়োজন বিধায় সংলগ্নী *** এর মাধ্যমে চাহিদা করা হয়েছে। উক্ত ঔষধসমুহ এএফএমএসডি’তে মজুত এবং&nbsp;Rate Running Contract এ না থাকায় জরুরি দরপত্র আহবানের মাধ্যমে স্থানীয়ভাবে ক্রয় পূর্বক সরবরাহ করা প্রয়োজন।</p>')
    const [NotesheetDetails1, setNotesheetDetails1] = useState('<p>৩। &nbsp; উল্লেখিত ঔষধসমুহ স্থানীয়ভাবে ক্রয়ের জন্য আনুমানিক 0000( মাত্র) টাকা ব্যয় হবে,&nbsp;যা ডিজিএমএস বাজেট কোড নম্বর-*****() খাতে বরাদ্দকৃত বাজেট হতে সংকুলান করা সম্ভব হবে। ঔষধটি যথাযথ প্রক্রিয়া অনুসরণ পূর্বক ক্রয় ও সরবরাহ নিশ্চিত করা যেতে পারে।</p><p>৪। &nbsp; বর্ণিত ঔষধসমুহ জরুরী ভিত্তিতে সরবরাহ করা সম্ভব না হলে চিকিৎসা সেবা ব্যহত হওয়ার সম্ভাবনা রয়েছে। মানসম্মত চিকিৎসা কার্যক্রম চলমান রাখার স্বার্থে বিষয়টি অতীব গুরুত্বপুর্ন ও জরুরি বিবেচনায় প্রতিরক্ষা মন্ত্রনালয় পত্র নং- ২৩.০০.০০০০.০৫০.৯৯.০০৩.১৯-১০৫ তারিখ ২৯ সেপ্টেম্বর ২০২০ এর আওতায় পিপিআর ২০০৮ এর বিধি ৬১ তে বর্ণিত উন্মুক্ত দরপত্র পদ্ধতির পরিবর্তে বিধি ৬২ তে বর্ণিত সীমিত দরপত্র পদ্ধতি অনুসরণ পূর্বক ক্রয় ও সরবরাহ নিশ্চিত করা যেতে পারে।</p><p>৫। &nbsp; বিষয়টি ডিজিএমএস মহোদয়ের সদয় অনুমোদনের জন্য উপস্থাপন করা হলো।</p>')
    const [NotesheetnoChecking, setNotesheetnoChecking] = useState(false)
    const [NotesheetHeadClarkNote, setNotesheetHeadClarkNote] = useState('')
    const [isDentalType, setIsDentalType] = useState(0)
    const [IsRepairDemand, setIsRepairDemand] = useState(0)
    const [IsRateRunning, setIsRateRunning] = useState(0)
    const [IsCustomNo, setIsCustomNo] = useState(0)
    const [isMunirKeyboard, setIsMunirKeyboard] = useState(false)
    const [prices, setPrices] = useState({});
    const [itemPrices, setItemPrices] = useState({});


    useEffect(() => {
        axios.get(window.app_url + '/get_all_item_types').then((res) => {
            setItemTypeList(res.data)
        })

        setNotesheetDate(new Date())
        if (window.suggested_notesheet_no_prefix) {
            setNotesheetno(window.suggested_notesheet_no_prefix)
        }

    }, []);

    useEffect(() => {
        if (Notesheetno) {
            setNotesheetnoChecking(true);
            axios.get(window.app_url + '/uniq_notesheet/' + Notesheetno).then((res) => {
                if (res.data) {
                    setNotesheetNoError('Notesheet no. exists!');
                } else {
                    setNotesheetNoError('');
                }
                setNotesheetnoChecking(false);
            })
        }

    }, [Notesheetno, 500])

    useEffect(() => {
        if (NoteSheetDemandItemType) {
            setIsLoading(true)
            axios.get(window.app_url + `/demand_ready_for_noteshet?item_type=${NoteSheetDemandItemType.id}&is_dental=${isDentalType}&is_repair=${IsRepairDemand}&is_rate_running=${IsRateRunning}`).then((res) => {
                setIsLoading(false)

                // 🔍 Log the full response data
                // console.log("Fetched data:", res.data);
                let demand_notesheet = []
                res.data.forEach(demand_pvms => {
                    demand_notesheet.push({ ...demand_pvms, isSelected_for_notesheet: false })
                });
                setDemands(demand_notesheet)

                //   setDemandsLinks(res.data.links)
            })
        }
    }, [NoteSheetDemandItemType, isDentalType, IsRepairDemand, IsRateRunning])

    useEffect(() => {
        if (Page > 1) {
            setIsLoading(true)
            axios.get(window.app_url + `/demand_ready_for_noteshet?page=${Page}`).then((res) => {
                setIsLoading(false)

                let demand_notesheet = []
                res.data.data.forEach(demand_pvms => {
                    demand_notesheet.push({ ...demand_pvms, isSelected_for_notesheet: false })
                });
                setDemands(demand_notesheet)
                setDemandDetailsItem('')
                setDemandsLinks(res.data.links)
            })
        }

    }, [Page]);

    const handleClickShowDetails = (item) => {
        setDemandDetailsItem(item)
        setIsShowModal(true)
    }

    const handleSelectionDemandAll = () => {
        let checkAll = Demands && Demands.length > 0;

        if (checkAll) {
            for (let index = 0; index < Demands.length; index++) {
                const element = Demands[index];
                if (!element.isSelected_for_notesheet) {
                    checkAll = false;
                    break;
                }

            }
        }

        return checkAll;
    }

    const handleSlectionDemands = (id, value) => {
        if (id) {
            setDemands(prev => {
                let copy = [...prev];

                let finIndexofItem = copy.findIndex(item => item.id == id);

                if (finIndexofItem > -1) {
                    copy[finIndexofItem].isSelected_for_notesheet = value
                }

                return copy;
            })
        } else {
            setDemands(prev => {
                let copy = [...prev];

                for (let index = 0; index < copy.length; index++) {
                    copy[index].isSelected_for_notesheet = value
                }

                return copy;
            })
        }
    }

    const handleSelectedDemandPvmsforNotesheet = () => {
        let notesheetDemands = []
        let noteDemandsUniqPvms = []

        for (let index = 0; index < Demands.length; index++) {
            const element = Demands[index];
            if (element.isSelected_for_notesheet) {
                if (IsRepairDemand == 1) {
                    if (IsRateRunning == 1) {
                        if (element.demand_repair_p_v_m_s_rate_running_only_notesheet && element.demand_repair_p_v_m_s_rate_running_only_notesheet.length > 0) {
                            element.demand_repair_p_v_m_s_rate_running_only_notesheet.forEach(item => {
                                // noteDemandsUniqPvms.push({...item,isSelected: true});
                                let findIndex = noteDemandsUniqPvms.findIndex(i => i.p_v_m_s_id == item.p_v_m_s.id);
                                if (findIndex > -1) {
                                    noteDemandsUniqPvms[findIndex].demands.push(item);
                                    noteDemandsUniqPvms[findIndex].qty += parseInt(item.qty);
                                } else {
                                    noteDemandsUniqPvms.push({
                                        isSelected: true,
                                        nomenclature: item.p_v_m_s.nomenclature,
                                        item_type: item.p_v_m_s?.item_typename?.name,
                                        au: item.p_v_m_s?.unit_name?.name,
                                        qty: parseInt(item.approved_qty),
                                        demands: [item],
                                        pvms_id: item.p_v_m_s.pvms_id,
                                        id: item.id,
                                        supplier: item.supplier,
                                        issue_date: item.issue_date,
                                        installation_date: item.installation_date,
                                        authorized_machine: item.authorized_machine,
                                        existing_machine: item.existing_machine,
                                        running_machine: item.running_machine,
                                        disabled_machine: item.disabled_machine,
                                        p_v_m_s_id: item.p_v_m_s_id,
                                    })

                                }

                            })
                        }
                    } else {
                        if (element.demand_repair_p_v_m_s_only_notesheet && element.demand_repair_p_v_m_s_only_notesheet.length > 0) {
                            element.demand_repair_p_v_m_s_only_notesheet.forEach(item => {
                                // noteDemandsUniqPvms.push({...item,isSelected: true});
                                let findIndex = noteDemandsUniqPvms.findIndex(i => i.p_v_m_s_id == item.p_v_m_s.id);
                                if (findIndex > -1) {
                                    noteDemandsUniqPvms[findIndex].demands.push(item);
                                    noteDemandsUniqPvms[findIndex].qty += parseInt(item.qty);
                                } else {
                                    noteDemandsUniqPvms.push({
                                        isSelected: true,
                                        nomenclature: item.p_v_m_s.nomenclature,
                                        item_type: item.p_v_m_s?.item_typename?.name,
                                        au: item.p_v_m_s?.unit_name?.name,
                                        qty: parseInt(item.approved_qty),
                                        demands: [item],
                                        pvms_id: item.p_v_m_s.pvms_id,
                                        id: item.id,
                                        supplier: item.supplier,
                                        issue_date: item.issue_date,
                                        installation_date: item.installation_date,
                                        authorized_machine: item.authorized_machine,
                                        existing_machine: item.existing_machine,
                                        running_machine: item.running_machine,
                                        disabled_machine: item.disabled_machine,
                                        p_v_m_s_id: item.p_v_m_s_id,
                                    })

                                }

                            })
                        }
                    }
                } else {
                    if (IsRateRunning == 1) {
                        if (element.demand_p_v_m_s_rate_running_only_notesheet && element.demand_p_v_m_s_rate_running_only_notesheet.length > 0) {
                            notesheetDemands.push(element);
                            element.demand_p_v_m_s_rate_running_only_notesheet.forEach(item => {
                                // noteDemandsUniqPvms.push({...item,isSelected: true});
                                let findIndex = noteDemandsUniqPvms.findIndex(i => i.p_v_m_s_id == item.p_v_m_s.id);

                                if (findIndex > -1) {
                                    noteDemandsUniqPvms[findIndex].demands.push(item);
                                    noteDemandsUniqPvms[findIndex].qty += parseInt(item.qty);
                                } else {
                                    noteDemandsUniqPvms.push({
                                        isSelected: true,
                                        nomenclature: item.p_v_m_s.nomenclature,
                                        item_type: item.p_v_m_s?.item_typename?.name,
                                        au: item.p_v_m_s?.unit_name?.name,
                                        qty: parseInt(item.qty),
                                        demands: [item],
                                        pvms_id: item.p_v_m_s.pvms_id,
                                        id: item.id,
                                        p_v_m_s_id: item.p_v_m_s_id,
                                    })

                                }

                            })
                        }
                    } else {
                        if (element.demand_p_v_m_s_only_notesheet && element.demand_p_v_m_s_only_notesheet.length > 0) {
                            notesheetDemands.push(element);
                            console.log(element);
                            element.demand_p_v_m_s_only_notesheet.forEach(item => {
                                // noteDemandsUniqPvms.push({...item,isSelected: true});
                                let findIndex = noteDemandsUniqPvms.findIndex(i => i.p_v_m_s_id == item.p_v_m_s.id);
                                debugger
                                if (findIndex > -1) {
                                    noteDemandsUniqPvms[findIndex].demands.push(item);
                                    noteDemandsUniqPvms[findIndex].qty += parseInt(item.qty);
                                } else {
                                    noteDemandsUniqPvms.push({
                                        isSelected: true,
                                        nomenclature: item.p_v_m_s.nomenclature,
                                        item_type: item.p_v_m_s?.item_typename?.name,
                                        au: item.p_v_m_s?.unit_name?.name,
                                        qty: parseInt(item.qty),
                                        demands: [item],
                                        pvms_id: item.p_v_m_s.pvms_id,
                                        p_v_m_s_id: item.p_v_m_s_id,
                                        id: item.id
                                    })

                                }

                            })
                        }
                    }
                }






                // element.demand_p_v_m_s_only_notesheet.forEach(item => {
                //     let itemIndex = noteDemandsUniqPvms.findIndex(i => i.id == item.id);
                //     if(itemIndex>-1) {

                //     } else {
                //         noteDemandsUniqPvms.push({...item,isSelected: true, demnads:[{demand_no:element.uuid,demand_pvms_no:item.id,pbms_no:item.p_v_m_s_id,qty:item.qty,sub_org_id:element.sub_org_id}]});
                //     }
                // })
            }

        }

        setNotesheetUniqDemandPvmsList(noteDemandsUniqPvms)
        setNotesheetDemandList(notesheetDemands)
        setIsShowCreateNoteSheetModal(true);
    }

    const handleNotesheetSlectionDemandPvmsList = (id, value) => {
        setNotesheetUniqDemandPvmsList(prev => {
            let copy = [...prev]
            let findIndex = copy.findIndex(i => i.id == id);
            debugger
            if (findIndex > -1) {
                copy[findIndex].isSelected = value
            }

            return copy
        })
    }

    const handleCreateNoteSheetItems = () => {
        if (NotesheetnoChecking) {
            Swal.fire({
                icon: 'info',
                // title: 'Oops...',
                text: `Please wait Notesheet Number checking in progress!`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
            return;
        }

        if (!NotesheetBudget || !NotesheetDetails || !NotesheetDetails1) {
            if (!NotesheetDetails || !NotesheetDetails1) {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: `Please Provide Notesheet Details!`,
                    // footer: '<a href="">Why do I have this issue?</a>'
                })
            }
            if (!NotesheetBudget) {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: `Please Provide Notesheet Budget!`,
                    // footer: '<a href="">Why do I have this issue?</a>'
                })
            }

            return;
        }

        if (NotesheetNoError) {
            window.scroll(0, 0);
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: NotesheetNoError,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
            return;
        }

        if (!Notesheetno || Notesheetno.length < 1) {
            setNotesheetNoError('Please Fill Up Notesheet Number')
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `Please Provide Valid Notesheet no!`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
            window.scroll(0, 0);
            return;
        }
        let totalSelectedItems = NotesheetUniqDemandPvmsList.filter(i => i.isSelected);
        if (totalSelectedItems.length == 0) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `No PVMS items has been selected!`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
            return;
        }

        
        const updatedNotesheetDemandList = totalSelectedItems.map(item => ({
            ...item,
            unit_price: itemPrices[item.id] ?? 0 
        }));

        // Step 2: Prepare request data
        let request_data = {
            notesheetDemandList: updatedNotesheetDemandList, 
            notesheet_item_type: NoteSheetDemandItemType.id,
            total_items: totalSelectedItems.length,
            total_demands: NotesheetDemandList.length,
            NotesheetDate,
            notesheet_id: Notesheetno,
            notesheet_budget: NotesheetBudget,
            notesheet_details: NotesheetDetails,
            notesheet_details1: NotesheetDetails1,
            is_munir_keyboard: isMunirKeyboard,
            head_clark_note: NotesheetHeadClarkNote,
            is_dental: isDentalType,
            is_repair: IsRepairDemand,
            is_rate_running: IsRateRunning
        };

        // let request_data = {
        //     unit_prices:itemPrices,
        //     notesheetDemandList: totalSelectedItems,
        //     notesheet_item_type: NoteSheetDemandItemType.id,
        //     total_items: totalSelectedItems.length,
        //     total_demands: NotesheetDemandList.length,
        //     NotesheetDate,
        //     notesheet_id: Notesheetno,
        //     notesheet_budget: NotesheetBudget,
        //     notesheet_details: NotesheetDetails,
        //     notesheet_details1: NotesheetDetails1,
        //     is_munir_keyboard: isMunirKeyboard,
        //     head_clark_note: NotesheetHeadClarkNote,
        //     is_dental: isDentalType,
        //     is_repair: IsRepairDemand,
        //     is_rate_running: IsRateRunning
        // }
        setIsFormSubmited(true);
        console.log(request_data);
        axios.post(window.app_url + '/notesheet', request_data).then((res) => {
            
            setIsFormSubmited(false)
            window.location.href = window.note_sheet_url
        }).catch(() => {
            setIsFormSubmited(false)
        })
    }

    const handleChangeIsMunirKeyboard = (value) => {
        setIsMunirKeyboard(value)
        if (value) {
            window.$('.ck-editor__main').addClass('munir-bangla')
        } else {
            window.$('.ck-editor__main').removeClass('munir-bangla')
        }

    }

    // Calculate GrandPrice outside the JSX
    const grandlPrice = NotesheetUniqDemandPvmsList?.reduce((acc, item) => {
        const qty = parseInt(item?.qty) || 0;
        const price = parseFloat(item?.demands?.[0]?.p_v_m_s?.rate_running_contract?.[0]?.price);
        return acc + (isNaN(price) ? 0 : price * qty);
    }, 0);

    // handle prices if dont have
    const handlePriceChange = (index, value) => {
        setPrices((prev) => ({
        ...prev,
        [index]: parseFloat(value) || 0,
        }));
    };


  const storeItemIdWithValue = (id, value) => {
    setItemPrices((prev) => ({
        ...prev,
        [id]: parseFloat(value) || 0,
    }));
};

// Grand total for no rate running demand
let grandTotal2 = 0;

    return (
        <>
            <ModalComponent
                show={IsShowCreateNoteSheetModal}
                size={"xl"}
                handleClose={() => setIsShowCreateNoteSheetModal(false)}
                handleShow={() => setIsShowCreateNoteSheetModal(true)}
                modalTitle={
                    <div className='bg-success p-2 text-white f14'>
                        Notesheet Create
                    </div>}
            >
                <div className="row mb-2">
                    <div className="col-6">
                        <div>Notesheet Date </div>
                        {/* <div><input type="date" pattern="\d{2}-\d{2}-\d{4}" className="form-control" value={NotesheetDate} onChange={(e)=> setNotesheetDate(e.target.value)}/></div> */}
                        <div>
                            <DatePicker
                                className="form-control"
                                selected={NotesheetDate}
                                onChange={(date) => setNotesheetDate(date)}
                                dateFormat="dd/MM/yyyy"
                            />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className='d-flex justify-content-between'><div>Notesheet No <span className="text-danger">*</span></div>
                            <div className="position-relative custom-control custom-checkbox">
                                <input name="check" id="customTenderNo" type="checkbox" checked={IsCustomNo == 1} onChange={(e) => {
                                    if (e.target.checked) {
                                        setIsCustomNo(1)
                                    } else {
                                        setIsCustomNo(0)
                                    }
                                }} className="custom-control-input" />
                                <label for="customTenderNo" class="custom-control-label font-weight-bold f14">Custom No.</label>
                            </div>
                        </div>
                        <div>
                            {IsCustomNo == 1 ?
                                <input className='form-control' type='text' value={Notesheetno} onChange={(e) => setNotesheetno(e.target.value)} required />
                                : <PatternFormat value={Notesheetno} format={`##.##.###.###.##.###.##.${moment(NotesheetDate).format('DD')}.${moment(NotesheetDate).format('MM')}.${moment(NotesheetDate).format('YYYY')}`}
                                    allowEmptyFormatting
                                    mask="_" className='form-control'
                                    onChange={(e) => setNotesheetno(e.target.value)}
                                />}
                        </div>
                        {NotesheetNoError && <span className='text-danger'>{NotesheetNoError}</span>}
                    </div>
                    <div className='col-12 mt-2'>
                        <div>
                            Notesheet Top Details <span className="text-danger">*</span>{' '}
                            <input type='checkbox' onChange={(e) => handleChangeIsMunirKeyboard(e.target.checked)} checked={isMunirKeyboard} /> Munir Keyboard
                        </div>
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
                            data={NotesheetDetails}
                            onChange={(event, editor) => {
                                const data = editor.getData();
                                setNotesheetDetails(data)
                            }}
                            required
                        />
                    </div>
                    <div className='col-12 mt-2'>
                        <div>
                            Notesheet Bottom Details <span className="text-danger">*</span>{' '}
                            {/* <input type='checkbox' onChange={(e) => handleChangeIsMunirKeyboard(e.target.checked)} checked={isMunirKeyboard}/> Munir Keyboard */}
                        </div>
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
                            data={NotesheetDetails1}
                            onChange={(event, editor) => {
                                const data = editor.getData();
                                setNotesheetDetails1(data)
                            }}
                            required
                        />
                    </div>
                    <div className='col-12 mt-2'>
                        <div>Notesheet Budget Amount (<b>BDT</b>)<span className="text-danger">*</span></div>
                        <div><input required type="number" className="form-control" placeholder='Notesheet Budget' onChange={(e) => setNotesheetBudget(e.target.value)} /></div>
                    </div>
                </div>


                {NotesheetDemandList && NotesheetDemandList.map((item, index) => (
                    <div className='font-weight-bold'>
                        {`${index + 1}. ${item.dmd_unit?.name} ${item.demand_type?.name} no. ${item.uuid} Date: ${moment(item.created_at).format('Do MMMM, YYYY')}`}
                    </div>
                ))}

                <table className='table table-bordered my-4'>
                    <thead>
                        {IsRepairDemand == 1 ?
                            <tr>
                                <th>Sl.</th>
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>Supplier</th>
                                <th></th>
                                <th></th>
                                <th className='text-right pr-2'>Quantity</th>
                            </tr>
                            :
                            <tr>
                                <th>Sl.</th>
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>Itme Type</th>
                                <th>A/U</th>
                                <th>Unit Price</th>

                                <th className='text-right pr-2'>Quantity</th>
                                <th>Total Price</th>
                            </tr>
                        }
                    </thead>
                    <tbody>
                        {IsRepairDemand == 1 ?
                            <>
                                {NotesheetUniqDemandPvmsList && NotesheetUniqDemandPvmsList.map((item, index) =>
                                    <tr>
                                        <td>{index + 1}</td>
                                        <td>{item.pvms_id}</td>
                                        <td>{item.nomenclature}</td>
                                        <td>{item.supplier}</td>
                                        <td>
                                            <b>Received Date:</b> {item.issue_date}
                                            <br />
                                            <b>Installed Date:</b> {item.installation_date}
                                        </td>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td className='border-0'><b>Auth:</b> {item.authorized_machine}</td>
                                                    <td className='border-0'><b>Held:</b> {item.existing_machine}</td>
                                                </tr>
                                                <tr>
                                                    <td className='border-0'><b>Running:</b> {item.running_machine}</td>
                                                    <td className='border-0'><b>Unservicable:</b> {item.disabled_machine}</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td className='text-right pr-2'>{parseInt(item.qty)}</td>
                                    </tr>
                                )}
                            </>
                            :
                            <>
                                {/* {NotesheetUniqDemandPvmsList && NotesheetUniqDemandPvmsList.map((item, index) =>
                                    <tr>
                                        <td>{index + 1}</td>
                                        <td>{item.pvms_id}</td>
                                        <td>{item.nomenclature}</td>
                                        <td>{item.item_type}</td>
                                        <td>{item.au}</td>
                                        <td>10000</td>
                                        <td className='text-right pr-2'>{parseInt(item.qty)}</td>
                                    </tr>
                                )} */}


                                    {NotesheetUniqDemandPvmsList && NotesheetUniqDemandPvmsList.map((item, index) => {


                                      const staticPrice = parseFloat(
                                        item?.demands?.[0]?.p_v_m_s?.rate_running_contract?.[0]?.price ?? ""
                                    );
                                    const hasStaticPrice = !isNaN(staticPrice);
                                    const price = hasStaticPrice ? staticPrice : prices[index] || 0;
                                    const qty = parseFloat(item?.qty) || 0;
                                    const totalPrice = (qty * price).toFixed(2);
                                    grandTotal2 += parseFloat(totalPrice);



                                    return (
                                        <tr>
                                            <td>{index + 1}</td>
                                            <td>{item.pvms_id}</td>
                                            <td>{item.nomenclature}</td>
                                            <td>{item.item_type}</td>
                                            <td>{item.au}</td>
                                            <td className="text-center">
                                                {hasStaticPrice ? (
                                                staticPrice.toFixed(2)
                                                ) : (
                                                <input
                                                    type="number"
                                                    placeholder='Enter Price'
                                                    className="text-right border py-1"
                                                    value={prices[index] ?? ''}
                                                    onChange={(e) => {
                                                        handlePriceChange(index, e.target.value);
                                                        storeItemIdWithValue(item.id, e.target.value); 
                                                    }}
                                                />
                                                )}
                                            </td>


                                            <td className='text-right pr-2'>{parseInt(item.qty)}</td>
                                            <td className='text-right'>{totalPrice}</td>
                                        </tr>
                                    );
                                })}


                                {/* Total Row */}
                                <tr style={{ fontWeight: 'bold', backgroundColor: '#f9f9f9' }}>
                                    <td colSpan="6" className="text-right pr-2">Grand Total</td>
                                    <td colSpan="2" className="text-right pr-2">
                                       {grandlPrice
                                        ? (typeof grandlPrice === 'number' ? grandlPrice.toFixed(2) : '0.00')
                                        : grandTotal2.toFixed(2)}

                                    </td>
                                </tr>

                            </>
                        }
                    </tbody>
                </table>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1">Note</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" placeholder="Note" rows="3" value={NotesheetHeadClarkNote} onChange={(e) => setNotesheetHeadClarkNote(e.target.value)}></textarea>
                </div>
                <div className='text-right'>
                    <button className='btn btn-success' disabled={isFormSubmited} onClick={handleCreateNoteSheetItems}>{isFormSubmited ? "Creating..." : 'Create & Forward'}</button>
                </div>

            </ModalComponent>
            <ModalComponent
                show={IsShowModal}
                size={"xl"}
                handleClose={() => setIsShowModal(false)}
                handleShow={() => setIsShowModal(true)}
                modalTitle={
                    <div className='bg-success p-2 text-white f14'>
                        Demand Number
                        <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{DemandDetailsItem && DemandDetailsItem.uuid}</span>
                    </div>}
            >
                <table className='table table-bordered'>
                    <thead>
                        {IsRepairDemand != 1 ?
                            <tr>
                                <th>Sl.</th>
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>Item Type</th>
                                <th>A/U</th>
                                <th className='text-right pr-2'>Quantity</th>
                            </tr>
                            :
                            <tr>
                                <th>Sl.</th>
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>Supplier</th>
                                <th></th>
                                <th></th>
                                <th className='text-right pr-2'>Quantity</th>
                            </tr>
                        }
                    </thead>
                    <tbody>
                        {IsRepairDemand == 1 ?
                            <>
                                {IsRateRunning == 1 ? <>
                                    {DemandDetailsItem && DemandDetailsItem.demand_repair_p_v_m_s_rate_running_only_notesheet && DemandDetailsItem.demand_repair_p_v_m_s_rate_running_only_notesheet.map((item, index) => (
                                        <tr>
                                            <td>{index + 1}</td>
                                            <td>{item.p_v_m_s.pvms_id}</td>
                                            <td>{item.p_v_m_s.nomenclature}</td>
                                            <td>{item.supplier}</td>
                                            <td>
                                                <b>Received Date:</b> {item.issue_date}
                                                <br />
                                                <b>Installed Date:</b> {item.installation_date}
                                            </td>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td className='border-0'><b>Auth:</b> {item.authorized_machine}</td>
                                                        <td className='border-0'><b>Held:</b> {item.existing_machine}</td>
                                                    </tr>
                                                    <tr>
                                                        <td className='border-0'><b>Running:</b> {item.running_machine}</td>
                                                        <td className='border-0'><b>Unservicable:</b> {item.disabled_machine}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td className='text-right pr-2'>{parseInt(item.approved_qty)}</td>
                                        </tr>
                                    ))}
                                </> : <>
                                    {DemandDetailsItem && DemandDetailsItem.demand_repair_p_v_m_s_only_notesheet && DemandDetailsItem.demand_repair_p_v_m_s_only_notesheet.map((item, index) => (
                                        <tr>
                                            <td>{index + 1}</td>
                                            <td>{item.p_v_m_s.pvms_id}</td>
                                            <td>{item.p_v_m_s.nomenclature}</td>
                                            <td>{item.supplier}</td>
                                            <td>
                                                <b>Received Date:</b> {item.issue_date}
                                                <br />
                                                <b>Installed Date:</b> {item.installation_date}
                                            </td>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td className='border-0'><b>Auth:</b> {item.authorized_machine}</td>
                                                        <td className='border-0'><b>Held:</b> {item.existing_machine}</td>
                                                    </tr>
                                                    <tr>
                                                        <td className='border-0'><b>Running:</b> {item.running_machine}</td>
                                                        <td className='border-0'><b>Unservicable:</b> {item.disabled_machine}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td className='text-right pr-2'>{parseInt(item.approved_qty)}</td>
                                        </tr>
                                    ))}
                                </>}
                            </>
                            :
                            <>
                                {IsRateRunning == 1 ? <>
                                    {DemandDetailsItem && DemandDetailsItem.demand_p_v_m_s_rate_running_only_notesheet && DemandDetailsItem.demand_p_v_m_s_rate_running_only_notesheet.map((item, index) => (
                                        <tr>
                                            <td>{index + 1}</td>
                                            <td>{item.p_v_m_s.pvms_id}</td>
                                            <td>{item.p_v_m_s.nomenclature}</td>
                                            <td>{item.p_v_m_s?.item_typename?.name}</td>
                                            <td>{item.p_v_m_s?.unit_name?.name}</td>
                                            <td className='text-right pr-2'>{parseInt(item.qty)}</td>
                                        </tr>
                                    ))}
                                </>
                                    : <>
                                        {DemandDetailsItem && DemandDetailsItem.demand_p_v_m_s_only_notesheet && DemandDetailsItem.demand_p_v_m_s_only_notesheet.map((item, index) => (
                                            <tr>
                                                <td>{index + 1}</td>
                                                <td>{item.p_v_m_s.pvms_id}</td>
                                                <td>{item.p_v_m_s.nomenclature}</td>
                                                <td>{item.p_v_m_s?.item_typename?.name}</td>
                                                <td>{item.p_v_m_s?.unit_name?.name}</td>
                                                <td className='text-right pr-2'>{parseInt(item.qty)}</td>
                                            </tr>
                                        ))}
                                    </>}
                            </>
                        }
                    </tbody>
                </table>

            </ModalComponent>

            <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                <h5 className="f-14">Notesheet Preparation</h5>
            </div>
            <div className=''>
                <div className='col-3 m-2'>
                    <b>Notesheet Item Type: </b>
                    <select
                        onChange={(e) => {
                            let findItem = ItemTypeList.findIndex(i => i.id == e.target.value)
                            if (findItem > -1) {
                                setNoteSheetDemandItemType(ItemTypeList[findItem])
                            }
                        }} value={NoteSheetDemandItemType?.id}
                        className='form-control' required >
                        <option value="">Select Notesheet Item Type</option>
                        {ItemTypeList && ItemTypeList.map((val, key) => (
                            <>{val?.name != 'Dental' && <option key={key} value={val.id}>{val?.name}</option>}</>
                        ))}
                    </select>
                </div>
                {NoteSheetDemandItemType && (NoteSheetDemandItemType.id == 3 || NoteSheetDemandItemType.id == 1 || NoteSheetDemandItemType.id == 5) &&
                    <>
                        <div className='col-12 m-2'>
                            <b>Is Dental Item : </b><br />
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_dental_type" id="flexRadioDefault1" onChange={(e) => setIsDentalType(1)} checked={isDentalType == 1} />
                                <label class="form-check-label" for="flexRadioDefault1">
                                    Yes
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_dental_type" id="flexRadioDefault2" onChange={(e) => setIsDentalType(0)} checked={isDentalType == 0} />
                                <label class="form-check-label" for="flexRadioDefault2">
                                    NO
                                </label>
                            </div>
                        </div>

                    </>
                }
                {NoteSheetDemandItemType && NoteSheetDemandItemType.id == 1 &&
                    <div className='col-12 m-2'>
                        <b>Is Repair Demand : </b><br />
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_repair_demand" id="flexRadioDefaultRepair1" onChange={(e) => setIsRepairDemand(1)} checked={IsRepairDemand == 1} />
                            <label class="form-check-label" for="flexRadioDefaultRepair1">
                                Yes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_repair_demand" id="flexRadioDefaultRepair2" onChange={(e) => setIsRepairDemand(0)} checked={IsRepairDemand == 0} />
                            <label class="form-check-label" for="flexRadioDefaultRepair2">
                                NO
                            </label>
                        </div>
                    </div>}
                {NoteSheetDemandItemType && <div className='col-12 m-2'>
                    <b>Is Rate Running Notesheet: </b><br />
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_rr" id="flexRadioDefaultRR1" onChange={(e) => setIsRateRunning(1)} checked={IsRateRunning == 1} />
                        <label class="form-check-label" for="flexRadioDefaultRR1">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_rr" id="flexRadioDefaultRR2" onChange={(e) => setIsRateRunning(0)} checked={IsRateRunning == 0} />
                        <label class="form-check-label" for="flexRadioDefaultRR2">
                            NO
                        </label>
                    </div>
                </div>}
            </div>
            {NoteSheetDemandItemType && <>
                <table className="table table-bordered">
                    <thead>
                        <tr className='text-left'>
                            <th>
                                {Demands && Demands.length > 0 && <div className="position-relative custom-control custom-checkbox">
                                    <input name="check" id="exampleCheck" type="checkbox" checked={handleSelectionDemandAll()} onChange={(e) => handleSlectionDemands('', e.target.checked)} className="custom-control-input" />
                                    <label for="exampleCheck" class="custom-control-label">All</label>
                                </div>}
                            </th>
                            <th>Sl.</th>
                            <th className="">
                                Demand No

                            </th>
                            <th className="">
                                Demand Type
                            </th>
                            <th className="">
                                Item Type
                            </th>
                            <th className="">
                                Checked by DADGMS

                            </th>
                            <th className="">DMD Unit</th>
                            <th className="">Total Item</th>
                            <th className='text-center'>View Details</th>
                        </tr>
                    </thead>

                    <tbody>
                        {IsLoading &&
                            <tr className='text-center'>
                                <td colSpan={8} className=''>
                                    <div className="ball-pulse w-100">
                                        <div className='spinner-loader'></div>
                                        <div className='spinner-loader'></div>
                                        <div className='spinner-loader'></div>
                                    </div>

                                </td>

                            </tr>
                        }
                        {Demands && Demands.map((item, index) => (
                            <tr className='text-left'>
                                <td>
                                    <div className="position-relative custom-control custom-checkbox">
                                        <input className="form-check-input input-check-accent" type="checkbox" checked={item.isSelected_for_notesheet} onChange={(e) => handleSlectionDemands(item.id, e.target.checked)} id={`checkboxNoLabel_${index}`} value="" aria-label="..." />
                                    </div>
                                </td>
                                <td>{index + 1}</td>
                                <td>{item.uuid}</td>
                                <td>{item?.demand_type?.name}</td>
                                <td>{item?.demand_item_type?.name}</td>
                                <td>Yes</td>
                                <td>{item.dmd_unit?.name}</td>

                                <td>
                                    {IsRepairDemand == 1 ?
                                        <>{IsRateRunning == 1 ? item?.demand_repair_p_v_m_s_rate_running_only_notesheet?.length :
                                            item.demand_repair_p_v_m_s_only_notesheet?.length}</> :
                                        <>{IsRateRunning == 1 ? item?.demand_p_v_m_s_rate_running_only_notesheet?.length :
                                            item?.demand_p_v_m_s_only_notesheet?.length}</>}

                                </td>
                                <td className='text-center'>
                                    <div>
                                        <i className="pe-7s-note2 metismenu-icon cursor-pointer f24" onClick={() => handleClickShowDetails(item)}> </i>
                                    </div>
                                </td>
                            </tr>
                        ))}

                    </tbody>

                </table>
                {Demands && Demands.filter(item => item.isSelected_for_notesheet).length > 0 && <div className="text-right mb-2 mx-2">
                    <button className='btn btn-success' disabled={isFormSubmited} onClick={handleSelectedDemandPvmsforNotesheet}>{isFormSubmited ? 'Creating Notesheet...' : 'Create Notesheet'}</button>
                </div>}
                {Demands && Demands.length == 0 &&
                    <div className='text-center pb-2 font-weight-bold'>No {NoteSheetDemandItemType?.name} items Demand Available for create Notesheet!</div>
                }
            </>}
        </>
    )
}

if (document.getElementById('react-notesheet')) {
    createRoot(document.getElementById('react-notesheet')).render(<CreateEditNoteSheet />)
}
