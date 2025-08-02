import moment from 'moment'
import React, { useEffect, useState } from 'react'
import { render } from 'react-dom'
import Swal from 'sweetalert2'

function Track({TrackingModule}) {
  const [No, setNo] = useState('')
  const [Loading, setLoading] = useState('')
  const [userApprovalRole, setUserApprovalRole] = useState('')
  const [TrackData, setTrackData] = useState('')
  const [CsrId, setCsrId] = useState('')

  useEffect(() => {
    axios.get(window.app_url+'/user-approval-roles').then((res)=>{
        setUserApprovalRole(res.data)
    })
  }, [])

  useEffect(() => {
    setTrackData('')
    setNo('')
    setCsrId('')
  }, [TrackingModule])

  useEffect(() => {
    setTrackData('')
    setCsrId('')
  }, [No])


  const handleSubmit = e => {
    e.preventDefault();
    setLoading(true);

    let data = {
        'tracking_on' : TrackingModule,
        'traking_no' : No
    }
    axios.get(`${window.app_url}/report/tracking-info?tracking_on=${TrackingModule}&traking_no=${No}`).then((res) => {
        debugger
        setLoading(false);
        if(Array.isArray(res.data) || Object.keys(res.data).length !== 0) {
            setTrackData(res.data);
        } else {
            setTrackData('');
            window.scroll(0,0);
            Swal.fire({
                icon: 'info',
                text: "No Data Found!"
            })
        }
    }).catch((err) => {
        setLoading(false);
        if(err.response?.data?.message){
            window.scroll(0,0);
            Swal.fire({
                icon: 'error',
                text: err.response?.data?.message,
            })
        }

    })
  }

  const renderDataRows = () => {
    switch (TrackingModule) {
        case 'demand':
        case 'notesheet':
            return (
                <>
                {TrackData.approval.map((val) => (
                        <>
                        {(
                            val.role_name!='head_clark' &&
                            val.role_name!='oic' && val.role_name!='deputy_commandend' && val.role_name!='cmdt') &&
                        <tr className={`${val.action=='BACK' ? 'text-danger' : ''}`}>
                            <td>
                                <input type='checkbox' checked/> {userApprovalRole.find(i => i.role_key==val.role_name).role_name}
                                {val.action=='BACK' && <span><br/>(sent to reapprove)</span>}

                            </td>
                            <td>
                                {val.note}
                            </td>
                            <td>
                                {moment(val.created_at).format('lll') }
                            </td>
                        </tr>
                        }
                        </>
                    ))}
                </>
            )
            break;

        case 'csr':
            debugger
            return (
                <>
                {TrackData.find(item => item.id == CsrId) && TrackData.find(item => item.id == CsrId).csr_pvms_approval.map((val) => (
                        <>
                        {(
                            val.role_name!='head_clark' &&
                            val.role_name!='oic' && val.role_name!='deputy_commandend' && val.role_name!='cmdt') &&
                        <tr className={`${val.action=='BACK' ? 'text-danger' : ''}`}>
                            <td>
                                <input type='checkbox' checked/>
                                {
                                    val.role_name == 'hod' && TrackData.find(item => item.id == CsrId).hod ? <>{` ${TrackData.find(item => item.id == CsrId).hod.email} `}<span className="f12">({TrackData.find(item => item.id == CsrId).hod.name})</span></>
                                :
                                <>{userApprovalRole?.find(i => i.role_key==val.role_name)?.role_name}</>
                                }

                            </td>
                            <td>
                                {val.bidder && val.bidder.company_name}
                            </td>
                            <td>
                                {val.note}
                            </td>
                            <td>
                                {moment(val.created_at).format('lll') }
                            </td>
                        </tr>
                        }
                        </>
                    ))}
                </>
            )
            break;

        default:
            break;
    }
  }

  return (
    <div>
        <form onSubmit={handleSubmit}>
            <div className="row">
                <div className='col-md-4'>
                    <div className="form-group">
                        <label className='text-capitalize'>{TrackingModule} No</label>
                        <input type="text" className="form-control" value={No} onChange={(e)=> setNo(e.target.value)}
                            placeholder={`Enter ${TrackingModule} No`}
                            required
                        />
                    </div>
                </div>
            </div>
            <button type='submit' className='btn btn-success' disabled={Loading}>
                {Loading ? 'Track...' : 'Track'}
            </button>
        </form>
        {
            Loading ?
            <div className="ball-pulse w-100 my-2 text-center">
                <div className='spinner-loader'></div>
                <div className='spinner-loader'></div>
                <div className='spinner-loader'></div>
            </div>
            :
            <>
                {TrackData &&
                <div>
                    {TrackingModule == 'csr' && TrackData.length > 0 &&
                    <div className='d-flex align-items-center my-2'>
                        <div className='pr-2'>
                            <label>Csr PVMS</label>
                        </div>
                        <div>
                            <select className="form-control" value={CsrId} onChange={e => setCsrId(e.target.value)}>
                                <option value="">Select Csr Pvms</option>
                                {TrackData.map(item => (
                                    <option value={item.id}>
                                        {item?.p_v_m_s?.pvms_id}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>
                    }
                    {(TrackingModule != 'csr' || (TrackingModule == 'csr' && CsrId)) &&
                    <div className='antiquewhite-bg padding-10 my-4'>
                        <h5>Approvals</h5>
                        <table className='table'>
                            <thead>
                                <tr>
                                    <th>Approve By</th>
                                    {TrackingModule == 'csr' && <th>Approved Vendor</th>}
                                    <th className={TrackingModule == 'csr' ? 'width20-percent':'width50-percent'}>Remark</th>
                                    <th>Approved On</th>
                                </tr>
                            </thead>
                            <tbody>
                                {renderDataRows()}
                            </tbody>
                        </table>
                    </div>}
                </div>}
            </>
        }
    </div>
  )
}

export default Track
