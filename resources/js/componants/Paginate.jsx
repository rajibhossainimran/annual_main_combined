import React from 'react'

function Paginate({setPage, Page, Links =[]}) {
  return (
    <nav ariaLabel="Page navigation example nav-partent">
        <ul className="pagination justify-content-center m-0 nav-partent">
            {Links.map((item,index)=>(
                <>
                    {index == 0 && 
                        <li className={`page-item nav-partent border-0 ${item.url ? '':'disabled'}`}>
                            <a className="page-link nav-partent border-0" onClick={()=> {
                                if(item.url) {
                                    setPage(prev => prev-1)
                                }
                            }}
                            tabIndex="-1"> {"|<"}</a>
                        </li>
                    }
                    {
                        index>0 && index<Links.length-1 && 
                        <li className={`page-item ${item.active ? 'active':''}`}>
                            <a class={`page-link border-0 ${item.active ? 'nav-partent-active': 'nav-partent' }`} onClick={()=> {
                                if(item.url) {
                                    setPage(index)
                                }
                            }}
                            tabIndex="-1"> {index}</a>
                        </li>
                    }
                    {index == Links.length-1 && 
                        <li className={`page-item ${item.url ? '':'disabled'}`}>
                            <a class="page-link nav-partent border-0" onClick={()=> {
                                if(item.url) {
                                    setPage(prev => prev+1)
                                }
                            }}
                            tabIndex="-1"> {">|"}</a>
                        </li>
                    }
                </>
                
            ))}
        </ul>
    </nav>
  )
}

export default Paginate