import axios from './../util/axios'
import moment from 'moment';
import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';
import ModalComponent from '../../componants/ModalComponent';
import Paginate from '../../componants/Paginate';
import RemarksTemplate from '../../componants/RemarksTemplate';

export default function NotesheetDetails() {
    const [Page, setPage] = useState(0)
    const [PerPage, setPerPage] = useState(10)
    const [NotesheetLinks, setNotesheetLinks] = useState()
    const [IsLoading, setIsLoading] = useState(true)
    const [IsLoadingDelete, setIsLoadingDelete] = useState(false)
    const [IsShowModal, setIsShowModal] = useState(false)
    const [NotesheetItem, setNotesheetItem] = useState()
    const [NotesheetItemDemands, setNotesheetItemDemands] = useState()
    const [NotesheetList, setNotesheetList] = useState()
    const [NotesheetListNextSteps, setNotesheetListNextSteps] = useState()
    const [isFormSubmited, setIsFormSubmited] = useState(false)
    const [PermissionCreate, setPermissionCreate] = useState(false);
    const [GiveNotesheetApproval, setGiveNotesheetApproval] = useState(false);
    const [UserApproval, setUserApproval] = useState();
    const [currentApprovalSteps, setCurrentApprovalSteps] = useState([])
    const [approvalSteps, setApprovalSteps] = useState()
    const [NotesheetDemanList, setNotesheetDemanList] = useState([])
    const [Remarks, setRemarks] = useState('')
    const [userApprovalRole, setUserApprovalRole] = useState([])

    useEffect(() => {
        axios.get(window.app_url + '/user-approval-roles').then((res) => {
            setUserApprovalRole(res.data)
        })
        axios.get(window.app_url + '/get_all_notesheet').then((res) => {
            let pending_notesheet = res.data.pending_notesheet_for_user;
            let notesheet_data = res.data.notesheets.data.filter(item => !res.data.pending_notesheet_ids.includes(item.id));
            let pending_along_prev_notesheet = [...pending_notesheet, ...notesheet_data];
            let pending_notesheet_next_steps = res.data.pending_notesheet_nextsteps;
            let notesheet_next_steps = res.data.notesheet_next_steps.filter(item => !res.data.pending_notesheet_ids.includes(item.id));
            let pending_along_prev_notesheet_next_steps = [...pending_notesheet_next_steps, ...notesheet_next_steps];

            setNotesheetList(pending_along_prev_notesheet);
            setNotesheetLinks(res.data.notesheets.links);
            setNotesheetListNextSteps(pending_along_prev_notesheet_next_steps);
            setIsLoading(false)
        })
        axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
            if (res.data.user_approval_role) {
                if (res.data.user_approval_role.role_key == "head_clark") {
                    setPermissionCreate(true)
                }
                setUserApproval(res.data.user_approval_role);
            }
        })
        axios.get(window.app_url + '/notesheet-approval-steps').then((res) => {
            setApprovalSteps(res.data)
        })
    }, []);


    useEffect(() => {
        if (Page > 0 || PerPage != 10) {
            setIsLoading(true)
            axios.get(window.app_url + `/get_all_notesheet?page=${Page}&limit=${PerPage}`).then((res) => {
                setIsLoading(false)
                let pending_notesheet = res.data.pending_notesheet_for_user;
                let notesheet_data = res.data.notesheets.data.filter(item => !res.data.pending_notesheet_ids.includes(item.id));
                let pending_along_prev_notesheet = [...pending_notesheet, ...notesheet_data];
                let pending_notesheet_next_steps = res.data.pending_notesheet_nextsteps;
                let notesheet_next_steps = res.data.notesheet_next_steps.filter(item => !res.data.pending_notesheet_ids.includes(item.id));
                let pending_along_prev_notesheet_next_steps = [...pending_notesheet_next_steps, ...notesheet_next_steps];

                setNotesheetList(pending_along_prev_notesheet);
                setNotesheetLinks(res.data.notesheets.links);
                setNotesheetListNextSteps(pending_along_prev_notesheet_next_steps);
                // let demand_notesheet = []
                // res.data.data.forEach(demand_pvms => {
                //    demand_notesheet.push({...demand_pvms , isSelected_for_notesheet:false})
                // });
                // setDemands(demand_notesheet)
                // setNotesheetList('')
                // setDemandsLinks(res.data.links)
            })
        }

    }, [Page, PerPage]);

    useEffect(() => {
        if (!IsShowModal) {
            setGiveNotesheetApproval(false);
        }
    }, [IsShowModal])

    const handleApproveNotesheet = (item) => {
        setGiveNotesheetApproval(true);
        handleClickShowDetails(item);
    }

    const notesheetApprovalTypeSwitch = (notesheet_item_type, is_dental) => {

        if ((notesheet_item_type == 1 || notesheet_item_type == 3) && (is_dental == 1)) {
            setCurrentApprovalSteps(approvalSteps['Dental'])
        } else if (notesheet_item_type == 1) {
            setCurrentApprovalSteps(approvalSteps['EMProcurementSteps'])
        } else if (notesheet_item_type == 3) {
            setCurrentApprovalSteps(approvalSteps['Medicine'])
        } else if (notesheet_item_type == 4) {
            setCurrentApprovalSteps(approvalSteps['Reagent'])
        } else if (notesheet_item_type == 5) {
            setCurrentApprovalSteps(approvalSteps['Disposable'])
        }

        return;
    }

    const handleClickShowDetails = (item) => {
        setNotesheetItem(item)
        notesheetApprovalTypeSwitch(item.notesheet_item_type, item.is_dental);
        let note_sheet_item_demand = [];
        let notesheetDemands = [];
        for (let index = 0; index < item.notesheet_demand_p_v_m_s.length; index++) {
            const element = item.notesheet_demand_p_v_m_s[index];
            let findDemandIndex = notesheetDemands.findIndex(i => i.id == element.demand_id);

            if (findDemandIndex < 0) {
                notesheetDemands.push(element.demand)
            }

            let findIndex = note_sheet_item_demand.findIndex(i => i.pvms_id == element.pvms_id)
            if (findIndex > -1) {
                note_sheet_item_demand[findIndex].demands.push(element);
                if (element.demand_repair_p_v_m_s) {
                    note_sheet_item_demand[findIndex].qty += parseInt(element.demand_repair_p_v_m_s.approved_qty);
                } else {
                    note_sheet_item_demand[findIndex].qty += parseInt(element.demand_pvms.qty);
                }
            } else {
                if (element.demand_repair_p_v_m_s) {
                    note_sheet_item_demand.push({
                        nomenclature: element.demand_repair_p_v_m_s.p_v_m_s.nomenclature,
                        item_type: element.demand_repair_p_v_m_s.p_v_m_s?.item_typename?.name,
                        qty: parseInt(element.demand_repair_p_v_m_s.approved_qty),
                        demands: [element.demand_repair_p_v_m_s],
                        pvms_id: element.pvms_id,
                        pvms_no: element.demand_repair_p_v_m_s.p_v_m_s.pvms_id,
                        id: element.id,
                        supplier: element.demand_repair_p_v_m_s.supplier,
                        issue_date: element.demand_repair_p_v_m_s.issue_date,
                        installation_date: element.demand_repair_p_v_m_s.installation_date,
                        authorized_machine: element.demand_repair_p_v_m_s.authorized_machine,
                        existing_machine: element.demand_repair_p_v_m_s.existing_machine,
                        running_machine: element.demand_repair_p_v_m_s.running_machine,
                        disabled_machine: element.demand_repair_p_v_m_s.disabled_machine
                    })
                } else {
                    note_sheet_item_demand.push({
                        nomenclature: element.demand_pvms.p_v_m_s.nomenclature,
                        item_type: element.demand_pvms.p_v_m_s?.item_typename?.name,
                        au: element.demand_pvms.p_v_m_s?.unit_name?.name,
                        qty: parseInt(element.demand_pvms.qty),
                        demands: [element.demand_pvms],
                        pvms_id: element.pvms_id,
                        pvms_no: element.demand_pvms.p_v_m_s.pvms_id,
                        id: element.id
                    })
                }


            }

        }

        notesheetDemands.sort(function (a, b) {
            let dateA = new Date(a.demand_date);
            let dateB = new Date(b.demand_date);
            return dateA - dateB;
        });

        setNotesheetDemanList(notesheetDemands);
        setNotesheetItemDemands(note_sheet_item_demand);
        setIsShowModal(true)
    }

    const handleSubmitApprove = (item) => {

        const data = {
            notesheet: item,
            remark: Remarks
        }

        Swal.fire({
            icon: 'warning',
            text: 'Do you want to approve now ?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve Now',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((r) => {
            if (r.isConfirmed) {
                setIsFormSubmited(true);
                axios.post(window.app_url + '/notesheet-approve', data).then((res) => {
                    console.log(res.data);
                    window.location.reload();
                })
            }
        })


    }

    const handleDeleteNotesheet = (notesheet_id) => {
        Swal.fire({
            icon: 'warning',
            text: 'This operation will delete all the supporting data along with this notesheet. Do you want to delete this notesheet?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((r) => {
            if (r.isConfirmed) {
                setIsLoadingDelete(true);
                axios.delete(`${app_url}/notesheet/${notesheet_id}`).then((res) => {
                    setIsLoadingDelete(false);
                    window.location.reload();
                })
            }
        })
    }


    return (
        <>

            <ModalComponent
                show={IsShowModal}
                size={"xl"}
                handleClose={() => setIsShowModal(false)}
                handleShow={() => setIsShowModal(true)}
                modalTitle={
                    <div className='bg-success p-2 text-white f14'>
                        Notesheet Number
                        <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{NotesheetItem && NotesheetItem.notesheet_id}</span>
                    </div>}
            >
                {NotesheetItem && NotesheetItem.is_rate_running == 1 &&
                    <b>Rate Running Notesheet</b>
                }
                {NotesheetItem && NotesheetItem.notesheet_budget > 0 &&
                    <>
                        <div>
                            Notesheet Budget: {NotesheetItem.notesheet_budget.toLocaleString()} <b>BDT</b>
                        </div>

                    </>
                }

                {NotesheetItem && NotesheetItem.notesheet_details &&
                    <div>
                        <div>
                            Notesheet Top Details
                        </div>
                        <div className='overflow-auto'>
                            <div dangerouslySetInnerHTML={{
                                __html: NotesheetItem.notesheet_details,
                            }} className={NotesheetItem.is_munir_keyboard ? 'munir-bangla' : ''} />
                        </div>

                    </div>
                }
                {NotesheetItem && NotesheetItem.notesheet_details1 &&
                    <div>
                        <div>
                            Notesheet Bottom Details
                        </div>
                        <div className='overflow-auto'>
                            <div dangerouslySetInnerHTML={{
                                __html: NotesheetItem.notesheet_details1,
                            }} className={NotesheetItem.is_munir_keyboard ? 'munir-bangla' : ''} />
                        </div>

                    </div>
                }
                {NotesheetDemanList && NotesheetDemanList.map((item, index) => (
                    <div className='font-weight-bold'>
                        {item.demand_date ?
                            <>
                                {`${index + 1}. ${item.dmd_unit?.name} Letter No. ${item.uuid} Dated: ${moment(item.demand_date).format('Do MMMM, YYYY')}`}
                            </>
                            :
                            <>
                                {`${index + 1}. ${item.dmd_unit?.name} Letter No. ${item.uuid} Dated: ${moment(item.created_at).format('Do MMMM, YYYY')}`}
                            </>}
                    </div>
                ))}
                <table className='table table-bordered mt-2'>
                    <thead>
                        {NotesheetItem && NotesheetItem.is_repair == 1 ?
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
                            <tr className=''>
                                <th>Sl.</th>
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>Itme Type</th>
                                <th>A/U</th>
                                <th>Price</th>
                                <th className='text-right pr-2'>Quantity</th>
                            </tr>}
                    </thead>
                    <tbody>
                        {NotesheetItem && NotesheetItem?.is_repair == 1 ?
                            <>
                                {NotesheetItemDemands && NotesheetItemDemands.map((item, index) =>
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
                                {NotesheetItemDemands && NotesheetItemDemands.map((item, index) => (
                                    <tr >
                                        <td>{index + 1}</td>
                                        <td>{item.pvms_no}</td>
                                        <td>{item.nomenclature}</td>
                                        <td>{item.item_type}</td>
                                        <td>{item.au}</td>
                                        <td className='text-right pr-2'>{item.qty}</td>
                                    </tr>
                                ))}
                            </>
                        }
                    </tbody>
                </table>
                <div className='row mb-2'>
                    <div className='col-md-6'>
                        {NotesheetItem && NotesheetItem.approval && NotesheetItem.approval.length > 0 && <div className='antiquewhite-bg padding-10'>
                            <h5>Approvals</h5>
                            <table className='table'>
                                <thead>
                                    <tr>
                                        <th className='width50-percent'>Approve By</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {NotesheetItem.approval.map((val) => (
                                        <>
                                            {(val.role_name != 'head_clark') &&
                                                <tr>
                                                    <td>
                                                        <input type='checkbox' checked /> {' '}
                                                        {userApprovalRole?.find(i => i.role_key == val.role_name)?.role_name}
                                                        {/* {currentApprovalSteps.find(i => i.designation==val.role_name)?.name } */}
                                                    </td>
                                                    <td>
                                                        {val.note}
                                                    </td>
                                                </tr>
                                            }
                                        </>
                                    ))}
                                </tbody>
                            </table>
                            {/* {currentApprovalSteps.map((val, key) => (
                                <div>
                                    {key > 1 && <><input type='checkbox' checked={demandApproval.find(i => i.role_name==val.designation)}/> {val.name}</> }

                                </div>

                            ))} */}
                        </div>
                        }
                    </div>
                    <div className='col-md-6'>
                        {NotesheetItem && NotesheetItem.head_clark_note && <b>Head Clark Note</b>}
                        {NotesheetItem && NotesheetItem.head_clark_note && (
                            <p>
                                1. {NotesheetItem.head_clark_note}
                            </p>
                        )}

                        {GiveNotesheetApproval &&
                            <>
                                <RemarksTemplate changeData={setRemarks} type="notesheet" /><br />
                                <b>Add Your Remark</b>
                                <textarea value={Remarks} onChange={(e) => setRemarks(e.target.value)} className='form-control'></textarea>
                            </>
                        }

                    </div>
                </div>

                {GiveNotesheetApproval &&
                    <div className="text-right">
                        <button className="btn btn-success" disabled={isFormSubmited} onClick={() => handleSubmitApprove(NotesheetItem)}>
                            {NotesheetListNextSteps && NotesheetItem && NotesheetListNextSteps.find(i => i.id == NotesheetItem.id) &&
                                <>
                                    {NotesheetListNextSteps.find(i => i.id == NotesheetItem.id).step &&
                                        <>{isFormSubmited ? `${NotesheetListNextSteps.find(i => i.id == NotesheetItem.id).step.btnText}...` : `${NotesheetListNextSteps.find(i => i.id == NotesheetItem.id).step.btnText}`}</>

                                    }
                                </>
                            }
                        </button>
                    </div>
                }
            </ModalComponent>

            <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                <h5 className="f-14">Notesheet Preparation</h5>
                <div>
                    <Paginate setPage={setPage} Page={Page} Links={NotesheetLinks} />
                </div>
                {PermissionCreate && <div className="text-right">
                    <a className="nav-link" href={window.notesheet_create_url}>
                        <button className="btn-icon btnc btn-custom">
                            <i className="fa fa-plus btn-icon-wrapper"></i>
                            {' '} Create Notesheet
                        </button>
                    </a>
                </div>}
            </div>
            <div className='d-flex my-2 justify-content-end mx-2 align-items-center gap-2'>
                <div className='pr-2'>
                    <label>Per Page</label>
                </div>
                <div>
                    <select className="form-control" value={PerPage} onChange={e => setPerPage(e.target.value)}>
                        <option value={10}>10</option>
                        <option value={25}>25</option>
                        <option value={50}>50</option>
                        <option value={100}>100</option>
                    </select>
                </div>
            </div>
            {IsLoadingDelete && <div className='d-flex justify-content-center'>
                <div className='pr-2'>Notesheet Delete on Progress</div>
                <div>
                    <div className="ball-pulse w-100">
                        <div className='spinner-loader'></div>
                        <div className='spinner-loader'></div>
                        <div className='spinner-loader'></div>
                    </div>
                </div>
            </div>}
            <table className="table table-bordered">
                <thead>
                    <tr className=''>
                        {/* <th>
                        {Demands && Demands.length>0 && <div className="position-relative custom-control custom-checkbox">
                            <input name="check" id="exampleCheck" type="checkbox" checked={handleSelectionDemandAll()} onChange={(e) => handleSlectionDemands('',e.target.checked)} className="custom-control-input"/>
                            <label for="exampleCheck" class="custom-control-label">All</label>
                        </div>}
                    </th> */}
                        <th>Sl.</th>
                        <th className="">
                            Notesheet No
                        </th>
                        <th className="">
                            Notesheet Item Type
                        </th>

                        <th className="">
                            Status
                        </th>
                        <th className="">Total Item</th>
                        <th className="">Last Approval</th>
                        <th className="">Create Date</th>
                        <th className="text-left width13-percent">Action</th>
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
                    {NotesheetList && NotesheetList.map((item, index) => (
                        <tr className=''>
                            {/* <td>
                            <div className="position-relative custom-control custom-checkbox">
                            </div>
                        </td> */}
                            <td>{Page == 0 ? index + 1 + (Page * PerPage) : index + 1 + (Page * PerPage - PerPage)}</td>
                            {/* <td><div className='d-flex gap-1'>
                                {item.notesheet_id}
                                {UserApproval && UserApproval.role_key == NotesheetListNextSteps[index].step.designation && <span className="badge bg-success text-white ml-1">New</span>}
                            </div></td> */}

                            <td>
                                <div className="d-flex gap-1">
                                    {item.notesheet_id}

                                    {/* New Badge */}
                                    {UserApproval && 
                                    UserApproval.role_key === NotesheetListNextSteps[index].step.designation && (
                                    <span className="badge bg-success text-white ml-1">New</span>
                                    )}

                                    {/* Rate Running Flag (R) */}
                                    {NotesheetList[index]?.is_rate_running === 1 && (
                                    <span className="badge bg-warning text-dark ml-1">R</span>
                                    )}
                                </div>
                            </td>
                            <td>{item.notesheet_type?.name} {item.is_repair == 1 && <>(Repair)</>}</td>
                            <td className='text-uppercase'>{item.status}</td>

                            <td>
                                {item.total_items}

                            </td>
                            <td>
                                {userApprovalRole && item.last_approved_role && item.last_approved_role != 'head_clark' ? userApprovalRole?.find(i => i.role_key == item.last_approved_role)?.role_name : '-'}
                            </td>
                            <td>{new Date(item.created_at).toLocaleDateString('en-GB')}</td>
                            <td className='text-center d-flex justify-content-start align-items-center'>
                                {UserApproval && UserApproval.id == 2 && <a onClick={() => { handleDeleteNotesheet(item.id) }} className='mr-2' title='Notesheet Delete'>
                                    <i className="fa fa-trash metismenu-icon cursor-pointer f20 text-danger"> </i>
                                    <br />
                                    <span className='cursor-pointer'>Delete</span>
                                </a>}
                                <a target='_blank' href={window.app_url + '/notesheet/download/pdf/' + item.id} className='mr-2' title='Notesheet Notice Download'>
                                    <i className="fa fa-file-pdf metismenu-icon cursor-pointer f20"> </i>
                                    <br />
                                    Download
                                </a>
                                <div className='cursor-pointer' onClick={() => handleClickShowDetails(item)}>
                                    <i className="pe-7s-note2 metismenu-icon cursor-pointer f20" > </i>
                                    <br />
                                    View
                                </div>
                                {/* {console.log(NotesheetListNextSteps[index].step.designation)}
                            {console.log(UserApproval)} */}
                                {UserApproval && UserApproval.role_key == NotesheetListNextSteps[index].step.designation &&
                                    <div className='ml-2 cursor-pointer' onClick={() => handleApproveNotesheet(item)}>
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                            {/*! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. */}
                                            <style
                                                dangerouslySetInnerHTML={{
                                                    __html:
                                                        "\n                                                            svg {\n                                                                fill: #089c14\n                                                            }\n                                                        "
                                                }}
                                            />
                                            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                        </svg>
                                        <br />Approve
                                    </div>}
                            </td>
                        </tr>
                    ))}

                </tbody>

            </table>
            <div>
                <Paginate setPage={setPage} Page={Page} Links={NotesheetLinks} />
            </div>
        </>
    )
}

if (document.getElementById('react-notesheet-details')) {
    createRoot(document.getElementById('react-notesheet-details')).render(<NotesheetDetails />)
}
