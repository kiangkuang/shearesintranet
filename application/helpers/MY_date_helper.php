<?php

function getAcadYear()
{
	if (date('n') < 8) {
    	return (date('y')-1) . '/' . date('y');
	}
	return date('y') . '/' . (date('y')+1);
}
