import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import AsyncSelect from 'react-select/async';
import Swal from 'sweetalert2';
import axios from './../util/axios';

export default function CreateEditAnnualDemand() {
    const [Department,setDepartment] = useState('')
    const [financialYears, setFinancialYears] = useState([]);
    const [ItemTypes, setItemTypes] = useState([]);
    const [ItemType, setItemType] = useState('');
    const [financialYear, setFinancialYear] = useState('');
    const [ReadonlyFY, setReadonlyFY] = useState(false);
    const [PrevPvmsList, setPrevPvmsList] = useState('')
    const [PvmsList, setPvmsList] = useState('')
    const [CurrentApproval, setCurrentApproval] = useState('')
    const [isConfirmFormSubmited, setIsConfirmFormSubmited] = useState(false)
    const [isApproveFormSubmited, setIsApproveFormSubmited] = useState(false)
    const [UserInfo, setUserInfo] = useState('')
    const [ViewMode, setViewMode] = useState('')
    const [AnnualDemand, setAnnualDemand] = useState('')
    const [NivList, setNivList] = useState('')

    useEffect(() => {
        axios.get(`${window.app_url}/settings/financial-years/api`)
        .then((res) => {
            setFinancialYears(res.data)
        })
        axios.get(`${window.app_url}/item-type-list-api`)
        .then((res) => {
            setItemTypes(res.data)
        })
        let url = new URL(window.location.href);
        let params = new URLSearchParams(url.search);
        let paramValue = params.get('finatialYear');
        let modeValue = params.get('mode');

        if(paramValue) {
            setFinancialYear(paramValue);
            setReadonlyFY(true);
        }
        if(modeValue) {
            setViewMode(true);
        }
        axios.get(window.app_url+'/getLoogedUserApproval').then((res) => {
            setUserInfo(res.data);
            if(res.data.dept_id) {
                setDepartment(res.data.dept_id);
            }
        })
    },[])

    useEffect(() => {
        if(financialYear && Department) {
            // /annual-demand/api
            axios.get(`${window.app_url}/annual-demand/api?finacialYear=${financialYear}&department=${Department}`)
            .then((res) => {
                debugger
                if(res.data) {
                    let department_pvms = res.data?.annual_demand?.department_list[0].pvms_list;
                    setAnnualDemand(res.data?.annual_demand);
                    setPrevPvmsList(department_pvms);
                    setCurrentApproval(res.data?.current_approval)
                    setNivList(res.data.niv)
                } else {
                    setAnnualDemand('');
                    setPrevPvmsList('');
                    setCurrentApproval('');
                    setNivList('');
                }
            })
        }
    },[financialYear,Department])

    const handleChangeSelectDepartment = (item, select) => {
        setDepartment(item.data.id);
    }

    const handleSelectPvms = (item, select) => {
        debugger
        if((PvmsList && PvmsList.find(i => i.id == item.data.id)) || (PrevPvmsList && PrevPvmsList.find(i => i.p_v_m_s.id == item.data.id))) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `This pvms has been already added.`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
        } else {
            setPvmsList(prev => {
                let copy = [...prev];
                copy.push(item.data);
                return copy;
            })
        }
    }

    const handleDeletePVMS = (item) => {
        setPvmsList(prev => {
            let copy = [...prev];
            copy = copy.filter(i => i.id != item.id);
            return copy;
        })
    }
    const handleDeletePrevPVMS = (item) => {
        Swal.fire({
            icon:'warning',
            text:'Do you want to remove this pvms now ?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((r) => {
            if(r.isConfirmed){
                axios.delete(`${window.app_url}/annual-demand-remove-pvms/${item.id}`)
                .then((res) => {
                    setPrevPvmsList(prev => {
                        let copy = [...prev];
                        copy = copy.filter(i => i.id != item.id);
                        return copy;
                    })

                    if(NivList && NivList.length>0) {
                        setNivList(prev => {
                            let copy = [...prev];
                            copy = copy.filter(i => i.p_v_m_s.id != item.p_v_m_s.id);
                            return copy;
                        })

                    }
                })
            }
        })


    }

    const handleConfirmSaveList = () => {
        if(!Department || !financialYear) {
            Swal.fire({
                icon: 'error',
                text: 'Department Name & Financial Year Required',
            })
        }
        setIsConfirmFormSubmited(true);
        let data = {
            'financial_year_id' : financialYear,
            'department_id' : Department,
            'pvms_list' : PvmsList
        }

        axios.post(window.app_url+'/annual_demand', data).then((res) => {
            window.location.href = window.annual_demand_index_url
            setIsConfirmFormSubmited(false)

        }).catch((err) => {
            setIsConfirmFormSubmited(false)
            if(err.response?.data?.message){
                window.scroll(0,0);
                Swal.fire({
                    icon: 'error',
                    text: err.response?.data?.message,
                })
            }

        })
    }


    const loadDepartmentOptions = (inputValue, callback) => {
        axios.get(window.app_url + '/department-list-api?search=' + inputValue).then((res) => {
          const data = res.data;

          let option = [];
          for (const iterator of data) {
            option.push({ value: iterator.id, label: iterator.name, data: iterator })
          }

          callback(option);
        })
    };

    const loadPvmsWIthStockOptions = (inputValue, callback) => {
        axios.get(window.app_url + '/pvms-with-stock-api?search=' + inputValue + '&item_type='+ItemType).then((res) => {
          const data = res.data;

          let option = [];
          for (const iterator of data) {
            option.push({value:iterator.id, label:iterator.pvms_id+' - '+iterator.nomenclature+' - '+ (iterator.pvms_old_name ? iterator.pvms_old_name : 'N/A'), data:iterator})
          }

          callback(option);
        })
    };

    const handleApproveDemandList = () => {
        debugger
        if(NivList && NivList.length > 0) {
            Swal.fire({
                icon: 'error',
                text: `Cannot perform this action. ${NivList.length} NIV PVMS exists. Update this PVMS to perform this action.`,
            })

            return;
        }
        const data = {
           id : AnnualDemand?.id,
           note: ''
        }

        Swal.fire({
            icon:'warning',
            text:'Are you sure you have finished checking the list of all departments and want to approve it now?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve Now',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((r) => {
            if(r.isConfirmed){
                setIsApproveFormSubmited(true);
                axios.post(app_url+'/annual-demand/approve', data).then((res) => {
                    console.log(res.data);
                    setIsApproveFormSubmited(false);
                    window.location.href = window.annual_demand_index_url;
                })
            }
        })
    }

    return(
    <>
        <div className='row p-2'>
            <div className='col-6'>
                <div className="form-group">
                    <label>Deparment Name</label>
                    <AsyncSelect cacheOptions name='department' loadOptions={loadDepartmentOptions} onChange={handleChangeSelectDepartment} placeholder="Department Name" defaultOptions />
                </div>
            </div>
            <div className='col-6'>
                <label>Financial Years</label>
                <select disabled={ReadonlyFY} className='form-control' required value={financialYear} onChange={(e) => setFinancialYear(e.target.value)}>
                  <option value="">Select</option>
                  {financialYears.map((val, key) => (
                    <option key={key} value={val.id}>{val.name}</option>
                  ))}
                </select>
            </div>
            <div className='col-6'>
                <label>Item Types</label>
                <select className='form-control' required value={ItemType} onChange={(e) => setItemType(e.target.value)}>
                  <option value="">Select</option>
                  {ItemTypes.map((val, key) => (
                    <option key={key} value={val.id}>{val.name}</option>
                  ))}
                </select>
                {/* <div className="form-group">
                    <label>Issue Voucher No</label>
                    <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleSelectVoucherNo} placeholder="Issue Voucher No" />
                </div> */}
            </div>

        </div>
        <table className='table table-bordered my-2 p-2'>
            <thead>
                <tr className=''>
                    <th>Sl.</th>
                    <th>PVMS No.</th>
                    <th>Nomenclature</th>
                    <th>A/U</th>
                    {!ViewMode && (!AnnualDemand || (AnnualDemand && !AnnualDemand.is_list_approved)) && <th className=''>Action</th>}
                </tr>
            </thead>
            <tbody>
                {PrevPvmsList && PrevPvmsList.sort((a,b) => {return a?.p_v_m_s?.nomenclature.localeCompare(b?.p_v_m_s?.nomenclature)})?.map((item,index) => (
                    <tr className=''>
                        <td>{index+1}</td>
                        <td>{item?.p_v_m_s?.pvms_id}</td>
                        <td>{item?.p_v_m_s?.nomenclature}</td>
                        <td>{item?.p_v_m_s?.unit_name?.name}</td>
                        {!ViewMode && (!AnnualDemand || (AnnualDemand && !AnnualDemand.is_list_approved)) &&<td className=''>
                            <button className='btn' type="button" onClick={() => handleDeletePrevPVMS(item)}>
                                <i className='pe-7s-close-circle text-danger f20 font-weight-bold'></i>
                            </button>
                        </td>}
                    </tr>
                ))}
                {PvmsList && PvmsList?.map((item,index) => (
                    <tr className=''>
                        <td>{PrevPvmsList ? index+1+PrevPvmsList.length : index+1}</td>
                        <td>{item?.pvms_id}</td>
                        <td>{item.nomenclature}</td>
                        <td>{item?.unit_name?.name}</td>
                        <td className=''>
                            <button className='btn' type="button" onClick={() => handleDeletePVMS(item)}>
                                <i className='pe-7s-close-circle text-danger f20 font-weight-bold'></i>
                            </button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>

        {!ViewMode && (!AnnualDemand || (AnnualDemand && !AnnualDemand.is_list_approved)) &&
        <div className='row my-3'>
            <div className='col-md-12 gap-2'>
                <b className='mb-2 px-2'>Search PVMS</b>
                <div className='px-2'>
                    <AsyncSelect cacheOptions loadOptions={loadPvmsWIthStockOptions} onChange={handleSelectPvms} value={''} placeholder="Search PMVS" defaultOptions/>
                </div>
            </div>
        </div>}

        {(!AnnualDemand || (AnnualDemand && !AnnualDemand.is_list_approved)) && <div className="text-right p-2">
            <div className='d-flex gap-2'>
                {Department && CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && (CurrentApproval.org != 'afmsd' || (CurrentApproval.org == 'afmsd' && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD')) &&
                <button className="btn btn-success mr-2" disabled={isApproveFormSubmited} onClick={() => handleApproveDemandList()}>
                    <>{isApproveFormSubmited ? `${CurrentApproval.btn_text}...`:`${CurrentApproval.btn_text}`}</>
                </button>}
                {PvmsList && PvmsList.length > 0 &&
                    <button className="btn btn-success" disabled={isConfirmFormSubmited} onClick={() => handleConfirmSaveList()}>
                        <>{isConfirmFormSubmited ? `Save...`:`Save`}</>
                    </button>
                }
            </div>
        </div>}
    </>
)}

if (document.getElementById('react-annual-demand')) {
    createRoot(document.getElementById('react-annual-demand')).render(<CreateEditAnnualDemand />)
}
