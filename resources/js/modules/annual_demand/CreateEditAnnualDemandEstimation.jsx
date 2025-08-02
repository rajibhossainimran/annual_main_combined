import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import AsyncSelect from 'react-select/async';
import Swal from 'sweetalert2';
import axios from './../util/axios';
import ModalComponent from '../../componants/ModalComponent';
import Paginate from '../../componants/Paginate';

export default function CreateEditAnnualDemandEstimation() {
    const [Page, setPage] = useState(0)
    const [PerPage, setPerPage] = useState(50)
    const [Department, setDepartment] = useState('')
    const [financialYears, setFinancialYears] = useState([]);
    const [ItemTypes, setItemTypes] = useState([]);
    const [ItemType, setItemType] = useState('');
    const [financialYear, setFinancialYear] = useState('');
    const [ReadonlyFY, setReadonlyFY] = useState(false);
    const [SelectedPvms, setSelectedPvms] = useState('')
    const [PvmsList, setPvmsList] = useState('')
    const [CurrentApproval, setCurrentApproval] = useState('')
    const [isConfirmFormSubmited, setIsConfirmFormSubmited] = useState(false)
    const [isApproveFormSubmited, setIsApproveFormSubmited] = useState(false)
    const [UserInfo, setUserInfo] = useState('')
    const [ViewMode, setViewMode] = useState('')
    const [AnnualDemand, setAnnualDemand] = useState('')
    const [AnnualDemandPvms, setAnnualDemandPvms] = useState('')
    const [AnnualUnitDemand, setAnnualUnitDemand] = useState('')
    const [Unit, setUnit] = useState('')
    const [UnitStock, setUnitStock] = useState('')
    const [NivList, setNivList] = useState('')
    const [IsReady, setIsReady] = useState('')
    const [isLoading, setisLoading] = useState(false)
    const [isSavingCurrentProgress, setisSavingCurrentProgress] = useState(false)
    const [IsShowModal, setIsShowModal] = useState(false)

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

        if (paramValue) {
            setFinancialYear(paramValue);
            setReadonlyFY(true);
        }
        if (modeValue) {
            setViewMode(true);
        }

        axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
            setUserInfo(res.data);
            // if(res.data.dept_id) {
            //     setDepartment(res.data.dept_id);
            // }
        })
    }, [])

    const pageChange = async (page) => {
        if (!ViewMode && AnnualDemand && AnnualDemand.is_list_approved && !AnnualDemand.is_unit_approved) {
            if ((PvmsList && PvmsList.length > 0) && ((UserInfo && UserInfo.user_approval_role && ((UserInfo.user_approval_role?.role_key == 'cmh_clark') || (CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && (UserInfo.user_approval_role.role_key == 'head_clark' || (UserInfo.sub_organization.type == 'AFMSD' && UserInfo.user_approval_role.role_key == 'cmh_clark'))))) || (AnnualDemand && AnnualDemand.is_list_approved && !AnnualDemand.is_unit_approved && CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && (CurrentApproval.org != 'afmsd' || (CurrentApproval.org == 'afmsd' && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD'))))) {
                await saveCurrentProgress();
            }
        }

        setPage(page);
    }

    useEffect(() => {
        if (financialYear) {
            setisLoading(true);
            axios.get(`${window.app_url}/annual-unit-demand/api?finacialYear=${financialYear}&page=${Page}&limit=${PerPage}&item=${ItemType}`)
                .then((res) => {
                    if (res.data) {
                        if (res.data.type == 'create') {
                            let department_pvms = res.data && res.data?.annual_demand_department_pvms ? res.data?.annual_demand_department_pvms?.map(item => {
                                return {
                                    ...item, estimated_qty: 0,
                                    annual_demand_pvms_unit_demand_id: '',
                                    annual_demand_pvms_id: item.id,
                                    unit_remarks: '',
                                }
                            }) : [];
                            setPvmsList(department_pvms);
                            setIsReady('');
                        } else if (res.data.type == 'update') {
                            let department_pvms = res.data && res.data?.annual_demand_unit_pvms ? res.data?.annual_demand_unit_pvms?.map(item => {
                                return {
                                    ...item,
                                    annual_demand_pvms_unit_demand_id: item.id,
                                    annual_demand_pvms_id: item?.annual_demand_pvms?.p_v_m_s?.id,
                                    p_v_m_s: item?.annual_demand_pvms?.p_v_m_s,
                                    afmsd_qty: item.afmsd_qty ? item.afmsd_qty : item.estimated_qty,
                                    dg_qty: item.dg_qty ? item.dg_qty : item.afmsd_qty,
                                }
                            }) : [];
                            setPvmsList(department_pvms);
                            if (res.data.annual_demand_unit) {
                                setIsReady(res.data.annual_demand_unit.is_ready);
                            }
                        }
                        setAnnualDemand(res.data?.annual_demand);
                        setAnnualDemandPvms(res.data?.annual_demand_pvms);
                        setCurrentApproval(res.data?.current_approval);
                        setUnitStock(res.data.unit_stock);

                    } else {
                        setAnnualDemand('');
                        setCurrentApproval('');
                        setUnitStock('');
                        setIsReady('');
                    }
                    setisLoading(false);
                }).catch(() => {
                    setisLoading(false);
                })
        }
    }, [financialYear, ItemType, Page, PerPage])


    const handleConfirmSaveList = () => {
        setIsConfirmFormSubmited(true);
        let data = {
            'financial_year_id': financialYear,
            'department_id': Department,
            'annual_demand_id': AnnualDemand.id,
            'annual_demand_pvms_list': PvmsList,
            'is_ready': IsReady
        }

        axios.post(window.app_url + '/annual-demand/unit-estimation', data).then((res) => {
            setIsConfirmFormSubmited(false)
            window.location.href = window.annual_demand_index_url

        }).catch((err) => {
            setIsConfirmFormSubmited(false)
            if (err.response?.data?.message) {
                window.scroll(0, 0);
                Swal.fire({
                    icon: 'error',
                    text: err.response?.data?.message,
                })
            }

        })
    }

    const handlechangeEstQty = (e, item, key, type = 'number') => {
        if (type == 'number') {
            if (isNaN(e.target.value)) {
                Swal.fire({
                    icon: 'error',
                    text: 'Enter Number',
                })
            }
        }
        setPvmsList(prev => {
            let copy = [...prev];
            let findIndex = copy.findIndex(i => i.id == item.id);

            if (findIndex > -1) {
                copy[findIndex] = { ...copy[findIndex], [key]: e.target.value }
            }

            return copy;
        })
    }

    const saveCurrentProgress = async () => {
        setIsConfirmFormSubmited(true);
        setisSavingCurrentProgress(true);
        let data = {
            'financial_year_id': financialYear,
            'department_id': Department,
            'annual_demand_id': AnnualDemand.id,
            'annual_demand_pvms_list': PvmsList,
            'is_ready': IsReady
        }

        await axios.post(window.app_url + '/annual-demand/unit-estimation', data).then((res) => {
            setIsConfirmFormSubmited(false)
            setisSavingCurrentProgress(false)
            // window.location.href = window.annual_demand_index_url

        }).catch((err) => {
            setIsConfirmFormSubmited(false)
            setisSavingCurrentProgress(false)
            if (err.response?.data?.message) {
                window.scroll(0, 0);
                Swal.fire({
                    icon: 'error',
                    text: err.response?.data?.message,
                })
            }

        })
    }


    const handleApproveDemandList = async () => {

        await saveCurrentProgress();

        const data = {
            id: AnnualDemand?.id,
            note: ''
        }

        Swal.fire({
            icon: 'warning',
            text: 'Are you sure you have finished checking the demand of all departments and want to approve it now?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve Now',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((r) => {
            if (r.isConfirmed) {
                setIsApproveFormSubmited(true);

                if (UserInfo && (UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' || UserInfo.sub_organization.type == 'DGMS')) {
                    axios.post(app_url + '/annual-demand-unit-estimation/approve-by-dept', data).then((res) => {
                        console.log(res.data);
                        setIsApproveFormSubmited(false);
                        window.location.href = window.annual_demand_index_url;
                    })
                } else {
                    axios.post(app_url + '/annual-demand-unit-estimation/approve', data).then((res) => {
                        console.log(res.data);
                        setIsApproveFormSubmited(false);
                        window.location.href = window.annual_demand_index_url;
                    })
                }

            }
        })
    }

    const handleClickOpenUnitEstimation = (item) => {
        setSelectedPvms(item.p_v_m_s.id);
        setIsShowModal(true);
    }

    return (
        <>
            {isSavingCurrentProgress &&
                <div id="loaderOverlay" class="loader-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                    <div className='pl-2'>Saving current progress</div>
                </div>}
            <ModalComponent
                show={IsShowModal}
                size={"xl"}
                handleClose={() => setIsShowModal(false)}
                handleShow={() => setIsShowModal(true)}
                modalTitle={
                    <div className='bg-success p-2 text-white f14'>
                        Unit Estimation
                    </div>}
            >{console.log(SelectedPvms)}
                {PvmsList && SelectedPvms && PvmsList.find(i => i.p_v_m_s.id == SelectedPvms) && <>
                    <div>PVMS No - {PvmsList.find(i => i.p_v_m_s.id == SelectedPvms)?.p_v_m_s?.pvms_id}</div>
                    <div>Nomenclature - {PvmsList.find(i => i.p_v_m_s.id == SelectedPvms)?.p_v_m_s?.nomenclature}</div>
                    <div>Unit - {PvmsList.find(i => i.p_v_m_s.id == SelectedPvms)?.p_v_m_s?.unit_name?.name}</div>
                </>}
                <table className='table table-bordered my-2 p-2'>
                    <thead>
                        <th>Unit</th>
                        <th>AFMSD Stock</th>
                        <th>Consumption <br />(Last 3 month)</th>
                        <th>Consumption <br />(Last 1 year)</th>
                        <th>Stock</th>
                        <th>
                            Unit Demand
                        </th>
                        <th>
                            Afmsd Approved
                        </th>
                        {UserInfo && (UserInfo.sub_organization?.type == 'DGMS' || !UserInfo.sub_organization) &&
                            <th>
                                Dgms Approved
                            </th>

                        }
                        <th>
                            Remarks
                        </th>
                    </thead>
                    <tbody>
                        {PvmsList && SelectedPvms && PvmsList.filter(i => i.p_v_m_s.id == SelectedPvms).map(item => (
                            <tr>
                                <td>{item?.annual_demand_unit?.sub_organization?.name}</td>
                                <td>
                                    {(UnitStock.find(i => i.id == item?.id) && UnitStock.find(i => i.id == item?.id).afmsd_stock_qty) ? UnitStock.find(i => i.id == item?.id).afmsd_stock_qty : 0}
                                </td>
                                <td className=''>
                                    {(UnitStock.find(i => i.id == item?.id) && UnitStock.find(i => i.id == item?.id).last_3_month_unit_consume_qty) ? UnitStock.find(i => i.id == item?.id).last_3_month_unit_consume_qty : 0}
                                </td>
                                <td className=''>
                                    {(UnitStock.find(i => i.id == item?.id) && UnitStock.find(i => i.id == item?.id).last_12_month_unit_consume_qty) ? UnitStock.find(i => i.id == item?.id).last_12_month_unit_consume_qty : 0}
                                </td>

                                <td className=''>
                                    {(UnitStock.find(i => i.id == item?.id) && UnitStock.find(i => i.id == item?.id).stock_qty) ? UnitStock.find(i => i.id == item?.id).stock_qty : 0}
                                </td>
                                <td>{item.estimated_qty}</td>
                                <td>
                                    {CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' ?
                                        <input type='text' value={item.afmsd_qty} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'afmsd_qty')} />
                                        :
                                        <>{item.afmsd_qty}</>
                                    }

                                </td>
                                {UserInfo && (UserInfo.sub_organization?.type == 'DGMS' || !UserInfo.sub_organization) &&
                                    <td>
                                        {CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && UserInfo.sub_organization && UserInfo.sub_organization.type == 'DGMS' ?
                                            <input type='text' value={item.dg_qty} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'dg_qty')} />
                                            :
                                            <>{item.dg_qty}</>
                                        }
                                    </td>

                                }
                                <td>
                                    {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' && <>
                                        {CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && UserInfo.user_approval_role.role_key == 'cmh_clark' ?
                                            <textarea value={item.afmsd_remarks} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'afmsd_remarks', 'text')} />
                                            :
                                            <>{item.afmsd_remarks}</>
                                        }
                                    </>}
                                    {UserInfo && UserInfo.sub_organization && (UserInfo.sub_organization?.type == 'DGMS' || !UserInfo.sub_organization) && <>
                                        {CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && UserInfo.user_approval_role.role_key == 'head_clark' ?
                                            <textarea value={item.dg_remarks} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'dg_remarks', 'text')} />
                                            :
                                            <>{item.dg_remarks}</>
                                        }
                                    </>}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>

            </ModalComponent>
            <div className='row p-2'>
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
                </div>

            </div>
            {isLoading ?
                <div className="text-center">
                    <div className="ball-pulse w-100">
                        <div className='spinner-loader'></div>
                        <div className='spinner-loader'></div>
                        <div className='spinner-loader'></div>
                    </div>
                </div>
                :
                <>
                    <div>
                        <Paginate setPage={pageChange} Page={Page} Links={AnnualDemandPvms && AnnualDemandPvms.links ? AnnualDemandPvms.links : []} />
                    </div>
                    <table className='table table-bordered my-2 p-2'>
                        <thead>
                            <tr className=''>
                                <th>Sl.</th>
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>A/U</th>
                                { }
                                {UserInfo && UserInfo.sub_organization && (UserInfo.sub_organization.type == 'AFMSD' || UserInfo.sub_organization.type == 'DGMS') ?
                                    <>
                                        <th>
                                            Total Unit Demand
                                        </th>
                                        <th>AFMSD Stock</th>
                                        { }
                                    </>
                                    :
                                    <>
                                        <th>Consumption <br />(Last 3 month)</th>
                                        <th>Consumption <br />(Last 1 year)</th>
                                        <th>Stock</th>
                                        <th>Est. Demand
                                            <br />(Last Year/4)*5
                                        </th>
                                    </>
                                }
                            </tr>
                        </thead>
                        <tbody>
                            {UserInfo && UserInfo.sub_organization && (UserInfo.sub_organization.type == 'AFMSD' || UserInfo.sub_organization.type == 'DGMS') ?
                                <>
                                    {AnnualDemandPvms && AnnualDemandPvms?.data && AnnualDemandPvms?.data?.sort((a, b) => { return a?.p_v_m_s?.nomenclature.localeCompare(b?.p_v_m_s?.nomenclature) })?.map((item, index) => (
                                        <tr>
                                            <td>{index + 1}</td>
                                            <td>{item?.p_v_m_s?.pvms_id}</td>
                                            <td>{item?.p_v_m_s?.nomenclature}</td>
                                            <td>{item?.p_v_m_s?.unit_name?.name}</td>
                                            <td>
                                                <div className='d-flex gap-3'>
                                                    <div>
                                                        {PvmsList.filter(i => i.p_v_m_s.id == item.p_v_m_s.id).reduce((prev, cur) => prev + cur.estimated_qty, 0)}
                                                    </div>
                                                    <div className='pl-2'>
                                                        <a onClick={() => handleClickOpenUnitEstimation(item)}><i className="fa fa-eye"></i></a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className=''>
                                                {(UnitStock.find(i => i.id == item?.id) && UnitStock.find(i => i.id == item?.id).afmsd_stock_qty) ? UnitStock.find(i => i.id == item?.id).afmsd_stock_qty : 0}
                                            </td>
                                        </tr>
                                    ))}
                                </>
                                :
                                <>
                                    {PvmsList && PvmsList?.sort((a, b) => { return a?.p_v_m_s?.nomenclature.localeCompare(b?.p_v_m_s?.nomenclature) })?.map((item, index) => (
                                        <tr className=''>
                                            <td>{index + 1}</td>
                                            <td>{item?.p_v_m_s?.pvms_name}</td>
                                            <td>{item?.p_v_m_s?.nomenclature}</td>
                                            <td>{item?.p_v_m_s?.unit_name?.name}</td>
                                            {UserInfo && UserInfo.sub_organization && (UserInfo.sub_organization.type == 'AFMSD' || UserInfo.sub_organization.type == 'DGMS') ?
                                                <>
                                                    <td className=''>
                                                        {(UnitStock.find(i => i.id == item?.id) && UnitStock.find(i => i.id == item?.id).afmsd_stock_qty) ? UnitStock.find(i => i.id == item?.id).afmsd_stock_qty : 0}
                                                    </td>
                                                    <td>
                                                        {item.estimated_qty}
                                                    </td>
                                                    <td>
                                                        {CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' && UserInfo.user_approval_role.role_key == 'cmh_clark' ?
                                                            <input type='text' value={item.afmsd_qty} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'afmsd_qty')} />
                                                            :
                                                            <>{item.afmsd_qty}</>
                                                        }
                                                    </td>
                                                    {UserInfo.sub_organization.type == 'DGMS' &&
                                                        <td>
                                                            {CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && UserInfo.sub_organization && UserInfo.sub_organization.type == 'DGMS' && UserInfo.user_approval_role.role_key == 'head_clark' ?
                                                                <input type='text' value={item.dg_qty} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'dg_qty')} />
                                                                :
                                                                <>{item.dg_qty}</>
                                                            }
                                                        </td>
                                                    }
                                                    <td>
                                                        {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' && <>
                                                            {CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && UserInfo.user_approval_role.role_key == 'cmh_clark' ?
                                                                <textarea value={item.afmsd_remarks} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'afmsd_remarks', 'text')} />
                                                                :
                                                                <>{item.afmsd_remarks}</>
                                                            }
                                                        </>}
                                                        {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'DGMS' && <>
                                                            {CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && UserInfo.user_approval_role.role_key == 'head_clark' ?
                                                                <textarea value={item.dg_remarks} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'dg_remarks', 'text')} />
                                                                :
                                                                <>{item.dg_remarks}</>
                                                            }
                                                        </>}
                                                    </td>
                                                </>
                                                :
                                                <>
                                                    <td className=''>
                                                        {(UnitStock?.find(i => i.id == item?.id) && UnitStock?.find(i => i.id == item?.id).last_3_month_unit_consume_qty) ? UnitStock.find(i => i.id == item?.id).last_3_month_unit_consume_qty : 0}
                                                    </td>
                                                    <td className=''>
                                                        {(UnitStock?.find(i => i.id == item?.id) && UnitStock?.find(i => i.id == item?.id).last_12_month_unit_consume_qty) ? UnitStock.find(i => i.id == item?.id).last_12_month_unit_consume_qty : 0}
                                                    </td>
                                                    <td className=''>
                                                        {(UnitStock?.find(i => i.id == item?.id) && UnitStock?.find(i => i.id == item?.id).stock_qty) ? UnitStock.find(i => i.id == item?.id).stock_qty : 0}
                                                    </td>
                                                    <td className=''>
                                                        {((!IsReady && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role?.role_key == 'cmh_clark') || (AnnualDemand && AnnualDemand.is_list_approved && !AnnualDemand.is_unit_approved && CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && (CurrentApproval.org != 'afmsd' || (CurrentApproval.org == 'afmsd' && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD')))) ?
                                                            <input type='text' value={item.estimated_qty} className='form-control' onChange={(e) => handlechangeEstQty(e, item, 'estimated_qty')} readOnly={ViewMode} />
                                                            :
                                                            <>{item.estimated_qty}</>
                                                        }
                                                    </td>
                                                </>
                                            }
                                        </tr>
                                    ))}
                                </>}
                        </tbody>
                    </table>
                    <div>
                        <Paginate setPage={pageChange} Page={Page} Links={AnnualDemandPvms && AnnualDemandPvms.links ? AnnualDemandPvms.links : []} />
                    </div>
                    {!ViewMode && <>
                        {AnnualDemandPvms && (AnnualDemandPvms.current_page == AnnualDemandPvms.last_page) && UserInfo && UserInfo.sub_organization.type != 'AFMSD' && UserInfo.user_approval_role && UserInfo.user_approval_role?.role_key == 'cmh_clark' && AnnualDemand && AnnualDemand.is_list_approved && !AnnualDemand.is_unit_approved && (PvmsList && PvmsList.length > 0) &&
                            <div className="position-relative custom-control custom-checkbox mb-2 ml-2">
                                <input name="check" id="exampleCheck" type="checkbox" checked={IsReady == 1} onChange={(e) => {
                                    if (e.target.checked) {
                                        setIsReady(1)
                                    } else {
                                        setIsReady(0)
                                    }
                                }} className="custom-control-input" />
                                <label htmlFor="exampleCheck" className="custom-control-label font-weight-bold f16">Checked & Forward for approval.</label>
                            </div>
                        }

                        {AnnualDemand && AnnualDemand.is_list_approved && !AnnualDemand.is_unit_approved && <div className="text-right p-2">
                            <div className='d-flex gap-2'>
                                {AnnualDemandPvms && (AnnualDemandPvms.current_page == AnnualDemandPvms.last_page) && CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && (CurrentApproval.org != 'afmsd' || (CurrentApproval.org == 'afmsd' && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD')) &&
                                    <button className="btn btn-success mr-2" disabled={isApproveFormSubmited} onClick={() => handleApproveDemandList()}>
                                        <>{isApproveFormSubmited ? `${CurrentApproval.btn_text}...` : `${CurrentApproval.btn_text}`}</>
                                    </button>}
                                {(PvmsList && PvmsList.length > 0) && ((UserInfo && UserInfo.user_approval_role && ((UserInfo.user_approval_role?.role_key == 'cmh_clark') || (CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && (UserInfo.user_approval_role.role_key == 'head_clark' || (UserInfo.sub_organization.type == 'AFMSD' && UserInfo.user_approval_role.role_key == 'cmh_clark'))))) || (AnnualDemand && AnnualDemand.is_list_approved && !AnnualDemand.is_unit_approved && CurrentApproval && UserInfo && UserInfo.user_approval_role && UserInfo.user_approval_role.role_key == CurrentApproval.designation && (CurrentApproval.org != 'afmsd' || (CurrentApproval.org == 'afmsd' && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD')))) &&
                                    <button className="btn btn-success" disabled={isConfirmFormSubmited} onClick={() => handleConfirmSaveList()}>
                                        <>{isConfirmFormSubmited ? `Save...` : `Save`}</>
                                    </button>
                                }
                            </div>
                        </div>}
                    </>}
                </>
            }
        </>
    )
}

if (document.getElementById('react-annual-demand-estimation')) {
    createRoot(document.getElementById('react-annual-demand-estimation')).render(<CreateEditAnnualDemandEstimation />)
}
