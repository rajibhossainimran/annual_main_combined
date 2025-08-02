import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import Select from 'react-select'
import ModalComponent from '../../componants/ModalComponent'
import moment from 'moment'
import Paginate from '../../componants/Paginate'

export default function Audit() {
    const section = [
        'User',
        'AccountUnit',
        'Store',
        'Division',
        'ItemDepartment',
        'ItemType',
        'ItemSection',
        'ItemGroup',
        'WarrantyType',
        'GoverningBody',
        'Organization',
        'FinancialYear',
        'Service',
        'Specification',
        'Supplier',
        'Role',
        'Permission',
        'Menu',
        'MenuHasPermission',
        'Tender',
        'Notesheet',
        'Demand',
        'Workorder',
        'AnnualDemand',
        'AnnualDemandListApproval',
        'AnnualDemandPvms',
        'AnnualDemandPvmsUnitDemand',
        'AnnualDemandUnitApproval'
    ]
    const operation = [
        'Create',
        'Update',
        'Delete',
        'Login',
        'Logout',
        'Approval'
    ]
    const [Sections, setSections] = useState(section.map(item =>{ return {value: item, label: item} }))
    const [Operations, setOperations] = useState(operation.map(item =>{ return {value: item, label: item} }))
    const [SelectedOperation, setSelectedOperation] = useState()
    const [SelectedSection, setSelectedSection] = useState()
    const [Search, setSearch] = useState()
    const [Page, setPage] = useState(1)
    const [PerPage, setPerPage] = useState(10)
    const [AuditData, setAuditData] = useState()
    const [IsLoading, setIsLoading] = useState(false)
    const [AuditDataLinks, setAuditDataLinks] = useState([])
    const [IsShowModal, setIsShowModal] = useState(false)
    const [AuditDetails, setAuditDetails] = useState()

    useEffect(() => {
        setIsLoading(true)
        let query = [];

        if(Page>0) {
            query.push(`page=${Page}`)
        }

        query.push(`limit=${PerPage}`)

        if(SelectedOperation) {
            query.push(`operation=${SelectedOperation}`)
        }

        if(SelectedSection) {
            query.push(`model=${SelectedSection}`)
        }

        if(Search) {
            query.push(`search=${Search}`)
        }

        let hit_url = `/audit-log?${query.join('&')}`;
        axios.get(hit_url).then((res)=>{
            setIsLoading(false);
            if(res.data) {
                if(res.data.data) {
                    setAuditData(res.data.data);
                }

                if(res.data.links){
                    setAuditDataLinks(res.data.links)
                }
            }
        })
    },[SelectedOperation,SelectedSection,Search,Page,PerPage])

    const debounce = (fn,delay) => {
        let timer;
        return (...args) => {
          if (timer) {
            clearTimeout(timer)
          }
          timer = setTimeout(() => {
            fn(...args);
          },delay)
        }
    }
    const handleSearchTextChange = debounce((e) => setSearch(e.target.value),500)

    const handleShowDetails = (id) => {
        let auditDetailsIndex = AuditData.findIndex(item => item.id == id);

        if(auditDetailsIndex > -1) {
            setAuditDetails(AuditData[auditDetailsIndex])
            setIsShowModal(true)
        }
    }

    return (
        <>
        <ModalComponent
            show={IsShowModal}
            handleClose={() => setIsShowModal(false)}
            handleShow={() => setIsShowModal(true)}
            modalTitle="Details"
        >

        </ModalComponent>
        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
            <h5 className="f-14">Audit</h5>
            <div>
               <Paginate setPage={setPage} Page={Page} Links={AuditDataLinks}/>
            </div>
            <div className="mr-2">
                <input className="form-control" Placeholder="Search..." type="text" onChange={handleSearchTextChange}/>
            </div>
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
        <table className="table table-striped table-bordered">
            <thead>
                <tr className=''>
                    <th className='sl-width'>Sl.</th>
                    <th className="width15-percent">
                        <div className='d-flex flex-column gap-2'>
                            <Select
                                className="basic-single"
                                classNamePrefix="select"
                                placeholder="Section"
                                // value={SelectedSection}
                                name="section"
                                options={Sections}
                                isSearchable={true}
                                isClearable={true}
                                onChange={(e) => setSelectedSection(e.value)}
                            />
                        </div>

                    </th>
                    <th className="width10-percent">
                        <div className='d-flex flex-column gap-2'>
                            <Select
                                className="basic-single"
                                classNamePrefix="select"
                                placeholder="Operation"
                                // value={SelectedOperation}
                                name="operation"
                                options={Operations}
                                isSearchable={true}
                                isClearable={true}
                                onChange={(e) => setSelectedOperation(e.value)}
                            />
                        </div>
                    </th>
                    <th className="width8-percent">IP</th>
                    <th className="">
                        <div className='d-flex align-items-start'>
                            <div>Description</div>
                        </div>

                    </th>
                    <th className="width10-percent">Perform By</th>

                    <th className="width15-percent">Perform On</th>
                    {/* <th className="">Action</th> */}
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
                {AuditData && AuditData.map((item,index)=>(
                    <tr className=''>
                        <td>{Page == 0 ? index+1+(Page*PerPage) : index+1+(Page*PerPage-PerPage)}</td>
                        <td>{item.model}</td>
                        <td>{item.operation}</td>
                        <td>{item.ip}</td>
                        <td>{item.description}</td>
                        <td>{item.performed_by.name}</td>
                        <td>
                        {item.created_at &&
                        <>
                            {moment(item.created_at).format('lll') }
                        </>}
                        </td>
                    </tr>
                ))}

            </tbody>

        </table>
            {AuditData && AuditData.length == 0 &&
                <div className='text-center'>No Log found!</div>
            }
            <div>
                <Paginate setPage={setPage} Page={Page} Links={AuditDataLinks}/>
            </div>
        </>
    )}

if (document.getElementById('react-audit')) {
    createRoot(document.getElementById('react-audit')).render(<Audit />)
}
