import axios from './../util/axios'
import moment from 'moment';
import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';
import RemarksTemplate from '../../componants/RemarksTemplate';

export default function Approval() {

    const [demand, setDemand] = useState()
    const [demandPVMS, setDemandPVMS] = useState([]);
    const [repairPVMS, setRepairPVMS] = useState([]);
    const [demandApproval, setDemandApproval] = useState([]);
    const [demadType, setDemandType] = useState()
    const [demadItemType, setDemandItemType] = useState()
    const [checkAll, setCheckAll] = useState(true)
    const [approvalRemark, setApprovalRemark] = useState('')
    const [approvalSteps, setApprovalSteps] = useState()
    const [currentApprovalSteps, setCurrentApprovalSteps] = useState([])
    const [viewType, setViewType] = useState()
    const [userApprovalRole, setUserApprovalRole] = useState([])
    const [canChangeAction, setCanChangeAction] = useState([])
    const [hods, setHods] = useState([])
    const [wings, setWings] = useState([])
    const [selectedWing, setSelectedWing] = useState()
    const [wingUsers, setWingUsers] = useState([])
    const [selectedWingUser, setSelectedWingUser] = useState()
    const [selectedHod, setSelectedHod] = useState()
    const [isLoading, setisLoading] = useState(false)
    const [isLoadingStock, setIsLoadingStock] = useState(false)
    const [StockData, setStockData] = useState('')
    const [userInfo, setUserInfo] = useState('')
    const [createdUserInfo, setCreatedUserInfo] = useState('')
    const [uuidValue, setUuidValue] = useState('');


    useEffect(() => {
        const approval = document.querySelectorAll('.approval')

        axios.get('/get-loged-user-approval-role').then((res)=>{
            window.user_approval_role = res.data
            setCanChangeAction(JSON.parse(res.data.can_change_action))
        })

        approval.forEach(element => {
            element.addEventListener('click', handleChangeApprovalClick)
        });

        axios.get(window.app_url+'/user-approval-roles').then((res)=>{
            setUserApprovalRole(res.data)
        })

        axios.get(window.app_url+'/getLoogedUserApproval').then((res) => {
            setUserInfo(res.data);
        })

    }, [])

    const currentFinantialYear = () => {
        const currentDate = moment();

        // Determine the financial year
        const currentYear = currentDate.format('YY');
        const fiscalYearStartMonth = 6; // Assuming April as the start month for the financial year
        const fiscalYear =  `${currentYear - 2} - ${currentYear-1}`;

        return `${fiscalYear}`
      }

    const handleChangeApprovalClick = (e) => {
        const demand_id = e.target.getAttribute('data-demand-id');
        const action = e.target.getAttribute('data-action');

        setViewType(action)
        if(demand_id) {
            setisLoading(true)
            axios.get(window.app_url + '/demand/api/' + demand_id).then((res) => {
                const data = res.data
                document.getElementById('demand-type-show').innerHTML = data.demand_item_type?.name;
                setCreatedUserInfo(res.data.created_by);
                if(data.demand_type_id==4){
                    axios.get(window.app_url + '/get-hod-users').then((res) => {
                        setHods(res.data)
                    })

                    axios.get(window.app_url + '/wings-json').then((res) => {
                        setWings(res.data)
                    })
                }

                setDemandType(data.demand_type_id)

                let pvms = []
                let pvms_id_list = [];
                for (const iterator of data.demand_p_v_m_s) {
                    const p_v_m_s = iterator.p_v_m_s

                    if(window.user_approval_role?.role_key!='oic' && iterator.co_selected==0){
                        // continue;
                    }

                    if(action=='approve'){
                        if(iterator.purchase_type=='issued' && !issueCanView()){
                            continue;
                        }

                        if((iterator.purchase_type=='lp' || iterator.purchase_type=='on-loan') && !lpAndOnloanCanView()){
                            continue;
                        }
                    }

                    if(!pvms_id_list.find(i => i == p_v_m_s.id)) {
                        pvms_id_list.push(iterator.p_v_m_s.id);
                    }

                    pvms.push({
                        id: iterator.id,
                        pvms_id: p_v_m_s.pvms_id,
                        nomenclature: p_v_m_s.nomenclature,
                        au: p_v_m_s.unit_name?.name,
                        // unit_pre_stock:iterator.unit_pre_stock,
                        // avg_expense:iterator.avg_expense,
                        patient_name: iterator.patient_name,
                        patient_id: iterator.patient_id,
                        disease: iterator.disease,
                        qty: iterator.qty,
                        remarks: iterator.remarks,
                        demand_pvms_id: iterator.id,
                        purchase_type: iterator.purchase_type,
                        co_note: iterator.co_note,
                        reviewd_qty: iterator.reviewd_qty ? iterator.reviewd_qty : iterator.qty,
                        central_stock: iterator.central_stock ? iterator.central_stock.qty : 0,
                        org_stock: iterator.org_stock ? iterator.org_stock.qty : 0,
                        authorized_machine:iterator.authorized_machine,
                        existing_machine:iterator.existing_machine,
                        running_machine:iterator.running_machine,
                        disabled_machine:iterator.disabled_machine,
                        ward:iterator.ward,
                        prev_purchase:iterator?.prev_purchase,
                        present_stock:iterator.present_stock ? iterator.present_stock : 0,
                        proposed_reqr:iterator?.proposed_reqr,
                        co_selected: true
                    })
                }

                let repair = []
                for (const iterator of data.demand_repair_p_v_m_s) {
                    console.log(iterator);
                    const p_v_m_s = iterator.p_v_m_s
                    if(!pvms_id_list.find(i => i == p_v_m_s.id)) {
                        pvms_id_list.push(iterator.p_v_m_s.id);
                    }
                    if(window.user_approval_role?.role_key!='oic' && iterator.co_selected==0){
                        // continue;
                    }

                    if(action=='approve'){
                        if(iterator.purchase_type=='issued' && !issueCanView()){
                            continue;
                        }

                        if((iterator.purchase_type=='lp' || iterator.purchase_type=='on-loan') && !lpAndOnloanCanView()){
                            continue;
                        }
                    }

                    repair.push({
                        id: iterator.id,
                        pvms_id: p_v_m_s.pvms_id,
                        nomenclature: p_v_m_s.nomenclature,
                        au: p_v_m_s.unit_name?.name,
                        supplier: iterator.supplier,
                        installation_date: moment(iterator.installation_date).format('DD/MM/Y'),
                        issue_date: moment(iterator.issue_date).format('DD/MM/Y'),
                        warranty_date: moment(iterator.warranty_date).format('DD/MM/Y'),
                        disabled_machine: iterator.disabled_machine,
                        running_machine: iterator.running_machine,
                        authorized_machine: iterator.authorized_machine,
                        existing_machine: iterator.existing_machine,
                        remarks: iterator.remarks,
                        demand_pvms_id: iterator.id,
                        purchase_type: iterator.purchase_type,
                        co_note: iterator.co_note,
                        co_selected: true
                    })
                }
                setIsLoadingStock(true);
                axios.get(window.app_url + '/pvms-list-api?pvms='+pvms_id_list.join(',')).then((res) => {
                    debugger
                    setStockData(res.data);
                    setIsLoadingStock(false);
                })
                setDemand({
                    id: data.id,
                    demand_type_id: data.demand_type_id,
                    status: data.status,
                    sub_org_id: data.sub_org_id,
                    last_approved_role: data.last_approved_role,
                    document_file: data.document_file,
                    demand_item_type_id: data.demand_item_type_id,
                    uuid: data.uuid
                })
                setDemandPVMS(pvms)
                setRepairPVMS(repair)
                setDemandApproval(data.approval)
                // demandApprovalTypeSwich(data.demand_item_type_id)
                // setDemandItemType(data.demand_item_type_id)
                setisLoading(false)
            })
        }

    }

    const demandApprovalTypeSwich = (demand_item_type) => {

        axios.get(window.app_url + '/demand-approval-steps').then((res) => {
            setApprovalSteps(res.data)

            const data = res.data

            switch (demand_item_type) {
                case 1:
                    setCurrentApprovalSteps(data['EMProcurementSteps'])
                    break;
                case 2:
                    setCurrentApprovalSteps(data['Dental'])
                    break;
                case 3:
                    setCurrentApprovalSteps(data['Medicine'])
                    break;
                case 4:
                    setCurrentApprovalSteps(data['Reagent'])
                    break;
                case 5:
                    setCurrentApprovalSteps(data['Disposable'])
                    break;

            }
        })


    }

    const handleChangePVMS = (e, index) => {
        setDemandPVMS((prev) => {
            let copy = [...prev]

            copy[index] = { ...copy[index], [e.name]: e.value };

            return copy
        })
    }

    const handleChangeRepairPVMS = (e, index) => {
        setRepairPVMS((prev) => {
            let copy = [...prev]

            copy[index] = { ...copy[index], [e.name]: e.value };

            return copy
        })
    }

    const handleChangePVMSChecked = (e, index) => {
        setDemandPVMS((prev) => {
            let copy = [...prev]

            copy[index] = { ...copy[index], [e.name]: e.checked };

            if(copy.length == copy.filter(i => i.co_selected).length){
                setCheckAll(true)
            }else{
                setCheckAll(false)
            }

            return copy
        })
    }

    // const handleSubmitApprove = (e) => {
    //     e.preventDefault()

    //     const data = {
    //         demand,
    //         demandPVMS,
    //         repairPVMS,
    //         approvalRemark,
    //         selectedHod,
    //         selectedWingUser
    //     }

    //     Swal.fire({
    //         icon:'warning',
    //         text:'Do you want to approve now ?',
    //         showCancelButton: true,
    //         confirmButtonText: 'Yes, Approve Now',
    //         cancelButtonText: 'No, cancel',
    //         reverseButtons: true
    //     }).then((r) => {
    //         if(r.isConfirmed){
    //             axios.post(app_url+'/demand-approve', data).then((res) => {
    //                 console.log(res.data);
    //                 window.location.reload();
    //             })
    //         }
    //     })


    // }


  




// const handleSubmitApprove = (e) => {
//         e.preventDefault();

//         //  Condition for 'oic' role to validate ID date
//         if (window.user_approval_role?.role_key === 'oic') {
//             const idParts = demand.uuid.split('.');
//             const isDateBasedId = idParts.length >= 4;

//             if (isDateBasedId) {
//                 const idDate = idParts.slice(-3).join('.');
//                 const today = new Date();
//                 const todayDate =
//                     String(today.getDate()).padStart(2, '0') + '.' +
//                     String(today.getMonth() + 1).padStart(2, '0') + '.' +
//                     today.getFullYear();

//                 if (idDate !== todayDate) {
//                     Swal.fire({
//                         icon: 'error',
//                         title: 'Date Mismatch',
//                         text: `Please Sir, give today's date. ID date: ${idDate}, Today: ${todayDate}`,
//                     });
//                     return;
//                 }
//             }
//         }

//         //  Condition for 'head_clark' role to check item_type vs email
//        if (window.user_approval_role?.role_key === 'head_clark') {
//                 const email = userInfo.email;
//                 const itemType = demand?.demand_item_type_id;

//                 console.log('Role:', window.user_approval_role?.role_key);
//                 console.log('Email:', email);
//                 console.log('Item Type:', itemType);

                
//                 if (email === 'clerk_dgms') {
//                     console.log('DGMS Clerk - full approval access granted');
//                     // no return, continue to approval
//                 } else {
//                     const approvalMap = {
//                         'clerk_medicine': 3,
//                         'clerk_disposable': 5,
//                         'clerk_EM': 1,
//                         'clerk_che_rgnt': 4,
//                     };

//                     if (approvalMap[email] !== itemType) {
//                         Swal.fire({
//                             icon: 'error',
//                             title: 'Approval Not Allowed',
//                             text: `You are not authorized to approve this item type.`,
//                         });
//                         return;
//                     }
//                 }
//             }


//         // If all checks passed, continue with approval
//         const data = {
//             demand,
//             demandPVMS,
//             repairPVMS,
//             approvalRemark,
//             selectedHod,
//             selectedWingUser,
//         };

//         Swal.fire({
//             icon: 'warning',
//             text: 'Do you want to approve now?',
//             showCancelButton: true,
//             confirmButtonText: 'Yes, Approve Now',
//             cancelButtonText: 'No, cancel',
//             reverseButtons: true,
//         }).then((r) => {
//             if (r.isConfirmed) {
//                 axios.post(app_url + '/demand-approve', data).then((res) => {
//                     console.log(res.data);
//                     window.location.reload();
//                 });
//             }
//         });
//     };



const handleSubmitApprove = (e) => {
    e.preventDefault();

    //  Condition for 'oic' role to validate ID date
    if (window.user_approval_role?.role_key === 'oic') {
        const idParts = demand.uuid.split('.');
        const isDateBasedId = idParts.length >= 4;

        if (isDateBasedId) {
            const idDate = idParts.slice(-3).join('.');
            const today = new Date();
            const todayDate =
                String(today.getDate()).padStart(2, '0') + '.' +
                String(today.getMonth() + 1).padStart(2, '0') + '.' +
                today.getFullYear();

            if (idDate !== todayDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Date Mismatch',
                    text: `Please Sir, give today's date.\nID date: ${idDate}, Today: ${todayDate}`,
                });
                return;
            }
        }
    }

    //  Condition for 'head_clark' role to check item_type vs email
    if (window.user_approval_role?.role_key === 'head_clark') {
        const email = userInfo.email;
        const itemType = demand?.demand_item_type_id;

        console.log('Role:', window.user_approval_role?.role_key);
        console.log('Email:', email);
        console.log('Item Type:', itemType);

        if (email === 'clerk_dgms') {
            console.log('DGMS Clerk - full approval access granted');
            // no return, continue to approval
        } else {
            const approvalMap = {
                'clerk_medicine': 3,
                'clerk_disposable': 5,
                'clerk_EM': 1,
                'clerk_che_rgnt': 4,
            };

            if (approvalMap[email] !== itemType) {
                Swal.fire({
                    icon: 'error',
                    title: 'Approval Not Allowed',
                    text: `You are not authorized to approve this item type.`,
                });
                return;
            }
        }
    }

    //  All checks passed — prepare data
    const data = {
        demand,
        demandPVMS,
        repairPVMS,
        approvalRemark,
        selectedHod,
        selectedWingUser,
    };

    //  SweetAlert confirmation before approving
    Swal.fire({
        icon: 'warning',
        text: 'Do you want to approve now?',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve Now',
        cancelButtonText: 'No, cancel',
        reverseButtons: true,
    }).then((r) => {
        if (r.isConfirmed) {
            axios.post(app_url + '/demand-approve', data)
                .then((res) => {
                    console.log(res.data);
                    window.location.reload();
                })
                .catch((err) => {
                    if (err.response?.status === 422) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Approval Failed',
                            text: err.response.data.message || 'Validation error occurred.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong during approval.',
                        });
                    }
                });
        }
    });
};


 const handleSave = (uuid, id) => {
    const data = {
        demand_name: uuid,
        demand_id: id,
    };

    Swal.fire({
        icon: 'warning',
        text: 'Do you want to save the updated UUID?',
        showCancelButton: true,
        confirmButtonText: 'Yes, Save',
        cancelButtonText: 'No, cancel',
        reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            axios.post(`${app_url}/demand-approve-changeuuid`, data)
                .then((res) => {
                    console.log("Saved:", res.data);
                    window.location.reload();
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const message = error.response.data.error || 'Validation failed.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: message,
                        });
                    } else {
                        console.error("Save failed:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong while saving.',
                        });
                    }
                });
        }
    });
};


//  const handleSubmitApprove = (e) => {
//     e.preventDefault();

//     const updatedUuid = e.target.uuid.value;

//     const data = {
//         demand,
//         demandPVMS,
//         repairPVMS,
//         approvalRemark,
//         selectedHod,
//         selectedWingUser,
//         demand_name: updatedUuid,
//     };

//     Swal.fire({
//         icon: 'warning',
//         text: 'Do you want to approve now?',
//         showCancelButton: true,
//         confirmButtonText: 'Yes, Approve Now',
//         cancelButtonText: 'No, cancel',
//         reverseButtons: true
//     }).then((r) => {
//         if (r.isConfirmed) {
//             axios.post(app_url + '/demand-approve', data)
//                 .then((res) => {
//                     console.log("Approved Demand ID:", res.data); 
//                     window.location.reload();
//                 })
//                 .catch((error) => {
//                     if (error.response && error.response.status === 422) {
//                         const message = error.response.data.error || 'Validation failed.';
//                         Swal.fire({
//                             icon: 'error',
//                             title: 'Validation Error',
//                             text: message,
//                         });
//                     } else {
//                         console.error("Approval failed:", error);
//                         Swal.fire({
//                             icon: 'error',
//                             title: 'Error',
//                             text: 'Something went wrong during approval.',
//                         });
//                     }
//                 });
//         }
//     });
// };




    const handleSelectAll = (is_checked) => {
        setCheckAll(is_checked)

        setDemandPVMS((prev) => {
            let copy = [...prev]

            copy = copy.map((val) =>  ({...val, co_selected: is_checked}));

            return copy
        })
    }

    const handleReapprovalSend = () => {
        const data = {
            demand,
            demandPVMS,
            approvalRemark
        }

        Swal.fire({
            icon:'warning',
            text:'Do you want to send for approve now ?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Send Now',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((r) => {
            if(r.isConfirmed){
                axios.post(app_url+'/demand-send-for-reapprove', data).then((res) => {
                    window.location.reload();
                })
            }
        })
    }

    const issueCanView = () => {
        const issue_can_view = ['oic', 'head_clark', 'cgo-1', 'gso-1', 'c&c', 'p&p'];

        return issue_can_view.includes(window.user_approval_role?.role_key)
    }

    const lpAndOnloanCanView = () => {
        const issue_can_view = ['oic', 'head_clark', 'cgo-1', 'gso-1', 'c&c', 'p&p', 'gso-2', 'ddgms'];

        return issue_can_view.includes(window.user_approval_role?.role_key)
    }

    const handleSelectedWing = (wing_id) => {
        setSelectedWing(wing_id);
        setSelectedWingUser()

        axios.get(window.app_url + '/wings/users/'+wing_id).then((res) => {
            setWingUsers(res.data)
        })
    }


const handleReturnToCmd = () => {
  const userId = window.user_approval_role?.id;
  const roleKey = window.user_approval_role?.role_key;
  const demandId = demand?.id;
  const remark = approvalRemark;

  if (!remark || remark.trim() === '') {
    Swal.fire('Warning!', 'Please Add Your Remark.', 'warning');
    return;
  }

  const data = {
    demand_id: demandId,
    approved_by: userId,
    role_name: roleKey,
    note: remark
  };

  Swal.fire({
    title: 'Return to CMD?',
    text: 'Do you want to return this demand to CMD?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, return it',
    cancelButtonText: 'No, cancel',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      axios.post(`${app_url}/return-demand-to-cmd`, data)
        .then(res => {
          Swal.fire('Success!', 'Demand returned to CMD.', 'success')
            .then(() => window.location.reload());
        })
        .catch(err => {
          console.error(err);
          Swal.fire('Error!', 'Something went wrong.', 'error');
        });
    }
  });
};





   


    return (
        <div>
           {isLoading ?
           <div className="text-center">
                <div className="ball-pulse w-100">
                    <div className='spinner-loader'></div>
                    <div className='spinner-loader'></div>
                    <div className='spinner-loader'></div>
                </div>
           </div>

           : <form onSubmit={handleSubmitApprove}>
                
                 <div className='bg-success p-2 text-white f14'>
                    Demand No
                    {window.user_approval_role?.role_key === 'oic' ? (
                        <div>
                        <input
                            type="text"
                            name="uuid"
                            defaultValue={demand?.uuid}
                            onChange={(e) => setUuidValue(e.target.value)}
                            className="bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-5"
                            style={{ width: '50%' }}
                        />
                        <button
                            type="button"
                            className="btn btn-primary mx-2"
                            onClick={() => handleSave(uuidValue, demand.id)}
                        >
                            Save
                        </button>
                        
                        </div>
                    ) : (
                        <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{demand?.uuid}</span>
                    )}
                </div>

                {demadType==4 ?
                <table className='table fixed-header-table'>
                    <thead>
                        <tr>
                            <th>
                                {demandApproval.length==0 && <input type='checkbox' checked={checkAll} onChange={(e) => handleSelectAll(e.target.checked)}/>} Sl
                            </th>
                            <th>PVMS.No</th>
                            <th>Nomenclature</th>
                            <th className='text-center'>Date</th>
                            <th className='text-center width-250'>No. Of Machines</th>
                            {(window.user_approval_role?.role_key=='cmh_clark' || window.user_approval_role?.role_key=='oic' || window.user_approval_role?.role_key=='mo' || window.user_approval_role?.role_key=='deputy_commandend' ||
                                window.user_approval_role?.role_key=='cmdt') && <th className='text-center'>Remarks</th>}
                            {window.user_approval_role?.role_key=='oic' && window.user_approval_role?.role_key=='head_clark' && <th>Co Note</th>}

                        </tr>
                    </thead>
                <tbody>
                    {repairPVMS.map((val, key) => (
                        <tr>
                            <td>
                                {demandApproval.length==0 && <input type='checkbox' name='co_selected' checked={val.co_selected} onChange={(e) => handleChangePVMSChecked(e.target, key)}/>} {key + 1}
                            </td>
                            <td>
                                {val.pvms_id}
                            </td>
                            <td>
                                {val.nomenclature}
                            </td>
                            <td>
                                <b>Issued :</b> {val.issue_date}<br/>
                                <b>Installation :</b> {val.installation_date}<br/>
                                <b>Warranty :</b> {val.warranty_date}
                            </td>
                            <td>
                                <div className='row'>
                                    <div className='col-6'>
                                        <b>Authorized : </b>  {val.authorized_machine}
                                    </div>
                                    <div className='col-6'>
                                        <b>Existing : </b>  {val.existing_machine}
                                    </div>
                                    <div className='col-6'>
                                        <b>Running : </b>  {val.running_machine}
                                    </div>
                                    <div className='col-6'>
                                        <b>Disabled : </b> {val.disabled_machine}
                                    </div>
                                </div>
                            </td>
                            {(window.user_approval_role?.role_key=='cmh_clark' || window.user_approval_role?.role_key=='oic' || window.user_approval_role?.role_key=='mo' || window.user_approval_role?.role_key=='deputy_commandend' ||
                                window.user_approval_role?.role_key=='cmdt') &&<td>{val.remarks}</td>}

                        </tr>
                    ))}

                </tbody>
                </table>
                :
                <table className='table fixed-header-table'>
                    <thead>
                        <tr>
                            <th>
                                {demandApproval.length==0 && <input type='checkbox' checked={checkAll} onChange={(e) => handleSelectAll(e.target.checked)}/>} Sl
                            </th>
                            {demadType == 1 && <th className='text-center'>Patient Name</th>}
                            <th>PVMS.No</th>
                            <th>Nomenclature</th>
                            {demadType == 1 && <th className='text-center'>Disease</th>}
                            <th className='text-center'>A/U</th>
                            {demand?.demand_item_type_id==1 && <th></th>}
                            <th className='text-right width-150'>Demand Qty.</th>
                            {(demandApproval && demandApproval.length>0 && window.user_approval_role?.role_key!='mo' && window.user_approval_role?.role_key!='cmh_clark') &&
                            <th className='text-right width-150'>Issue. Qty.</th>
                            }
                            <th className='text-center width-5' style={{width:'10%'}}>Stock</th>
                            {((userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2) || (createdUserInfo && createdUserInfo.sub_org_id == 2)) &&
                                <>

                                    <th className='text-center'>
                                        Avg. 3 month Previous
                                    </th>
                                    <th className='text-center'>
                                        Purchase <br/>{currentFinantialYear()}
                                    </th>
                                    <th className='text-center'>Present Stock</th>
                                    <th className='text-center'>Proposed Reqr</th>
                                </>
                            }
                            {(window.user_approval_role?.role_key=='cmh_clark' || window.user_approval_role?.role_key=='oic' || window.user_approval_role?.role_key=='mo') && <th className='text-center'>Remarks</th>}
                            {window.user_approval_role?.role_key=='oic' && window.user_approval_role?.role_key=='head_clark' && <th>Co Note</th>}
                            {window.user_approval_role?.role_key!='cmh_clark' && window.user_approval_role?.role_key!='mo' && window.user_approval_role?.role_key!='oic' && window.user_approval_role?.role_key!='head_clark' &&
                                <th>Action</th>
                            }
                        </tr>
                    </thead>
                    <tbody>
                        {demandPVMS.map((val, key) => (
                            <tr key={key}>
                                <td>
                                {demandApproval.length==0 && <input type='checkbox' name='co_selected' checked={val.co_selected} onChange={(e) => handleChangePVMSChecked(e.target, key)}/>} {key + 1}
                                </td>
                                {demadType == 1 && <th>{val.patient_name}</th>}
                                <td>
                                    {val.pvms_id}
                                </td>
                                <td>
                                    {val.nomenclature}
                                </td>
                                {demadType == 1 &&
                                    <td className='text-center'>
                                        {val.disease}
                                    </td>
                                }
                                <td className='text-center'>
                                    {val.au}
                                </td>

                                {demand?.demand_item_type_id==1 &&
                                <td>
                                    <div className='row s6'>
                                        <div className='col-6'>
                                            <b>Auth:</b> {val.authorized_machine}
                                        </div>
                                        <div className='col-6'>
                                            <b>Held:</b> {val.existing_machine}
                                        </div>
                                        <div className='col-6'>
                                            <b>Svc:</b> {val.running_machine}
                                        </div>
                                        <div className='col-6'>
                                            <b>Unsvc:</b> {val.disabled_machine}
                                        </div>
                                        <div className='col-12'>
                                            <b>Dept/Ward:</b> {val.ward}
                                        </div>
                                    </div>
                                </td>
                                }
                                <td className='text-right'>
                                    {(viewType=='view' || window.user_approval_role?.role_key!='oic' && window.user_approval_role?.role_key!='head_clark') && val.reviewd_qty}
                                    {(viewType!='view' && window.user_approval_role?.role_key=='head_clark') && val.reviewd_qty}
                                    {/* {(viewType=='view' || window.user_approval_role.role_key!='oic' && window.user_approval_role.role_key!='head_clark') && '/'+val.reviewd_qty} */}
                                    <input type={viewType!='view' && (window.user_approval_role?.role_key=='oic') ? 'number':'hidden'} required className="form-control text-right" name="qty" value={val.qty} onChange={(e) => handleChangePVMS(e.target, key)} />
                                    {/* <input type={viewType!='view' && (window.user_approval_role.role_key=='head_clark') ? 'number':'hidden'} required className="form-control text-right" name="reviewd_qty" value={val.reviewd_qty} onChange={(e) => handleChangePVMS(e.target, key)} /> */}

                                </td>
                                {(demandApproval && demandApproval.length>0 && window.user_approval_role?.role_key!='mo' && window.user_approval_role?.role_key!='cmh_clark') &&
                                <td className='text-right'>
                                    {window.user_approval_role?.role_key=='dgms' ||  window.user_approval_role?.role_key=='oic'?
                                    val.qty
                                    :
                                    <>{
                                        viewType=='view' ? <>
                                        {demandApproval && demandApproval.length<2 ? <></>:<>{val.qty}</>}
                                        </>:
                                        <>
                                        {window.user_approval_role?.role_key!='head_clark'
                                        && window.user_approval_role?.role_key!='cgo-1'
                                        && window.user_approval_role?.role_key!='c&c'
                                        && window.user_approval_role?.role_key!='p&p'
                                        && val.qty
                                        }
                                        <input type={window.user_approval_role?.role_key=='head_clark'
                                                    || window.user_approval_role?.role_key=='cgo-1'
                                                    || window.user_approval_role?.role_key=='c&c'
                                                    || window.user_approval_role?.role_key=='p&p'
                                                    ? 'number':'hidden'} name='qty' className='form-control text-right' value={val.qty} onChange={(e) => handleChangePVMS(e.target, key)}/>
                                        </>

                                    }

                                    </>

                                    }

                                </td>
                                }

                                <td className='text-center'>
                                    <span className='f12 font-weight-bold'>AFMSD:</span>
                                    {isLoadingStock ? '...' :
                                        StockData?.find(i => i.pvms_id == val.pvms_id) ?
                                        StockData?.find(i => i.pvms_id == val.pvms_id).afmsd_stock_qty ?
                                        StockData?.find(i => i.pvms_id == val.pvms_id).afmsd_stock_qty : 0
                                        :0
                                    }
                                    {/* {val.central_stock} */}
                                    <br/>
                                    <span className='f12 font-weight-bold'>Unit:</span>
                                    {isLoadingStock ? '...' :
                                        StockData?.find(i => i.pvms_id == val.pvms_id) ?
                                        StockData?.find(i => i.pvms_id == val.pvms_id).stock_qty ?
                                        StockData?.find(i => i.pvms_id == val.pvms_id).stock_qty : 0
                                        :0
                                    }
                                </td>
                                {((userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2) || (createdUserInfo && createdUserInfo.sub_org_id == 2)) &&
                                    <>
                                        <td className='text-center'>
                                        {isLoadingStock ? '...' :
                                            StockData?.find(i => i.pvms_id == val.pvms_id) ?
                                            StockData?.find(i => i.pvms_id == val.pvms_id).last_3_month_unit_consume_qty ?
                                            StockData?.find(i => i.pvms_id == val.pvms_id).last_3_month_unit_consume_qty : 0
                                            :0
                                        }
                                        </td>
                                        <td className='text-right'>
                                            {val.prev_purchase}
                                        </td>
                                        <td className='text-right'>
                                            {val.present_stock}
                                        </td>
                                        <td className='text-right'>{val.proposed_reqr}</td>
                                    </>
                                }
                                {(window.user_approval_role?.role_key=='cmh_clark' || window.user_approval_role?.role_key=='oic' || window.user_approval_role?.role_key=='mo') && <td>
                                    {val.remarks}
                                </td>}
                                { window.user_approval_role?.role_key=='oic' && window.user_approval_role?.role_key=='head_clark' && <td>
                                    {window.user_approval_role?.role_key!='oic' && window.user_approval_role?.role_key!='cmh_clark' && window.user_approval_role?.role_key!='mo' && val.co_note}
                                    {window.user_approval_role?.role_key=='oic' ?
                                    <>{viewType!='view' ? <textarea className='form-control' name='co_note' value={val.co_note} onChange={(e) => handleChangePVMS(e.target, key)}></textarea>:
                                    <>
                                    {val.co_note}
                                    <input type='hidden' name='co_note' value={val.co_note} onChange={(e) => handleChangePVMS(e.target, key)}/>
                                    </>

                                    }</>

                                    :
                                    <input type='hidden' name='co_note' value={val.co_note} onChange={(e) => handleChangePVMS(e.target, key)}/>
                                    }

                                </td>}
                                {window.user_approval_role?.role_key!='cmh_clark' &&
                                window.user_approval_role?.role_key!='mo' &&
                                window.user_approval_role?.role_key!='oic' &&
                                window.user_approval_role?.role_key!='head_clark' &&
                                window.user_approval_role?.role_key!='deputy_commandend' &&
                                window.user_approval_role?.role_key!='cmdt' &&
                                <td>
                                    <select hidden={viewType=='view'} className="form-control" name="purchase_type" value={val.purchase_type} disabled={canChangeAction.length == 0 || viewType=='view'} onChange={(e) => handleChangePVMS(e.target, key)}
                                        required={
                                            (demadItemType == 1 && window.user_approval_role?.role_key == 'cgo-1')
                                            ||
                                            ((demadItemType == 3 || demadItemType == 5) && window.user_approval_role?.role_key == 'c&c')
                                            ||
                                            (demadItemType == 4 && window.user_approval_role?.role_key == 'p&p')
                                        }
                                    >
                                        <option value=''>Select</option>
                                        <option value='lp' disabled={!canChangeAction.includes('lp')}>LP</option>
                                        <option value='notesheet' disabled={!canChangeAction.includes('notesheet')}>Notesheet</option>
                                        <option value='on-loan' disabled={!canChangeAction.includes('on-loan')}>On Loan</option>
                                        <option value='issued' disabled={!canChangeAction.includes('issued')}>Issued</option>
                                    </select>

                                    {viewType=='view' && <span className="text-uppercase">{val.purchase_type}</span>}

                                </td>
                                }

                            </tr>
                        ))}

                    </tbody>
                </table>
                }

                <div className='row'>
                    <div className='col-md-6'>
                        {demandApproval.length > 2 &&
                        window.user_approval_role?.role_key!='cmh_clark' &&
                        window.user_approval_role?.role_key!='mo' &&
                        window.user_approval_role?.role_key!='oic' &&
                        window.user_approval_role?.role_key!='deputy_commandend' &&
                        window.user_approval_role?.role_key!='cmdt' &&
                        <div className='antiquewhite-bg padding-10'>
                            <h5>Approvals</h5>
                            <table className='table'>
                                <thead>
                                    <tr>
                                        <th className='width50-percent'>Approve By</th>
                                        <th>Approved At</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   {demandApproval.map((val) => {
                                        console.log(val); 
                                        return (
                                       <>
                                            {/* Show all roles except the filtered ones */}
                                            {(val.role_name !== 'head_clark' &&
                                                val.role_name !== 'oic' &&
                                                val.role_name !== 'deputy_commandend' &&
                                                val.role_name !== 'cmdt' &&
                                                val.role_name !== 'mo') && (
                                                <tr className={`${val.action == 'BACK' ? 'text-danger' : ''}`}>
                                                <td>
                                                    <input type="checkbox" checked />{' '}
                                                    {userApprovalRole.find((i) => i.role_key == val.role_name)?.role_name}
                                                    {val.action == 'BACK' && <span><br />(sent to reapprove)</span>}
                                                </td>
                                                <td>
                                                    {(() => {
                                                    const date = new Date(val.created_at);
                                                    const formatted = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
                                                    return formatted;
                                                    })()}
                                                </td>
                                                <td>{val.note}</td>
                                                </tr>
                                            )}

                                            {/* Special row for head_clark to show update/approval date */}
                                            {val.role_name === 'head_clark' && (
                                                <tr className="text-primary">
                                                <td>
                                                    <input type="checkbox" checked /> Head Clark
                                                </td>
                                                <td>
                                                    {(() => {
                                                    const date = new Date(val.created_at);
                                                    const formatted = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
                                                    return formatted;
                                                    })()}
                                                </td>
                                                <td>{val.note}</td>
                                                </tr>
                                            )}
                                            </>

                                        );
                                    })}

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
                       {demand && demand.document_file && <a href={`${window.app_url}/storage/demand_documents/${demand?.document_file}`} target='_blank'> <i className='fa fa-download'></i> Uploaded Document</a>}<br/>
                        {window.user_approval_role?.role_key!='cmh_clark' && window.user_approval_role?.role_key!='mo' && <>
                        {(window.user_approval_role?.role_key=='head_clark' || window.user_approval_role?.role_key=='oic') &&
                            demandApproval.find(i => i.role_name=='oic' && i.note) &&
                            <div>
                                <div><b>CO Note</b></div>
                                <div>{ demandApproval.find(i => i.role_name=='oic' && i.note).note}</div>
                            </div>
                        }
                        {demandApproval.filter(i => i.role_name=='head_clark' && i.note).length > 0 && <b>Head Clark Note</b>}
                        {demandApproval.filter(i => i.role_name=='head_clark' && i.note).map((val, key) => (
                            <p>
                                {key+1}. {val.note}
                            </p>
                        ))}
                        </>}

                        {viewType!='view' &&
                        <>
                            <RemarksTemplate changeData={setApprovalRemark} type="demand"/><br/>
                            <b>Add Your Remark</b>
                            <textarea value={approvalRemark} onChange={(e) => setApprovalRemark(e.target.value)} className='form-control margin-btm'></textarea>
                        </>
                        }

                    </div>
                </div>

                       
                         
                <div className="modal-footer">

                          {demand?.status === 'Reapproval' && window.user_approval_role?.role_key === 'head_clark' && (
                                <button type='button' className="btn btn-danger mt-2" onClick={handleReturnToCmd}>
                                Return to cmd
                                </button>
                            )}
                           

                    <button type="button" className="btn btn-secondary" data-dismiss="modal">Close</button>
                    {viewType!='view' &&
                    <>
                    {(window.user_approval_role?.role_key!='head_clark' && window.user_approval_role?.role_key!='oic' && window.user_approval_role?.role_key!='cmh_clark' && window.user_approval_role?.role_key!='mo' && window.user_approval_role?.role_key!='deputy_commandend' && window.user_approval_role?.role_key!='cmdt') &&
                    <button type="button" className="btn btn-success" onClick={() => handleReapprovalSend()}>Send to Reapproval</button>}
                    {(demadType==4 && window.user_approval_role?.role_key == 'oic-repair') &&
                    <select className='form-control s5' onChange={(e) => setSelectedHod(e.target.value)} value={selectedHod} required>
                        <option value="">Select Hod</option>
                        {hods.map((val, key) => (
                            <option value={val.id}>{val.name}</option>
                        ))}
                    </select>
                    }
                    {(demadType==4 && window.user_approval_role?.role_key == 'hod') &&
                    <>
                        <select className='form-control s5' onChange={(e) => handleSelectedWing(e.target.value)} value={selectedWing} required>
                            <option value="">Select Wing</option>
                            {wings.map((val, key) => (
                                <option value={val.id}>{val.name}</option>
                            ))}
                        </select>
                        <select className='form-control s5' onChange={(e) => setSelectedWingUser(e.target.value)} value={selectedWingUser} required>
                            <option value="">Select Wing Head</option>
                            {wingUsers.map((val, key) => (
                                <option value={val.id}>{val.name}</option>
                            ))}
                        </select>
                    </>
                    }
                    <button type="submit" className="btn btn-primary">{window.user_approval_role?.role_key=='head_clark' ? 'Forward' : window.user_approval_role?.role_key=='ddgms' ? 'Seen':'Approve'}  </button>
                    </>
                    }

                </div>
            </form>}

        </div>
    )
}

if (document.getElementById('react-demand-approval')) {
    createRoot(document.getElementById('react-demand-approval')).render(<Approval />)
}
