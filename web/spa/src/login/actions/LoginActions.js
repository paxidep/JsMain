import * as CONSTANTS from '../../common/constants/apiConstants';
// import axios from 'axios';
import React from 'react';
import {push} from 'react-router-redux';  

export  function signin(email,password)
{
  return dispatch =>
  {
    fetch( CONSTANTS.API_SERVER +'/api/v1/api/login?email='+email+'&password='+password, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
      },
    })
    .then(response => response.json())
    .then( (response) => {
      dispatch({
        type:'SET_CHECKSUM',
        payload: response.AUTHCHECKSUM
      });
      dispatch(push('/myjs'));
    })
    .catch( (error) => {
      console.warn('Actions - fetchJobs - recreived error: ', error)
    })
  } 
}
